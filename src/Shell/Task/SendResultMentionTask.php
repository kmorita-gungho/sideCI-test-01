<?php
namespace App\Shell\Task;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * SendResultMention shell task.
 */
class SendResultMentionTask extends TwcsBaseTask
{
    private $campaign_key;  // キャンペーン識別キー
    private $retweets_id;   // リツイートID
    private const MOVIE_DIR = ROOT . '/data/Movies/';
    private const UNTREATED    = 0;  // 未処理
    private const NOT_COVERED  = 1;  // 対象外
    private const NOT_SELECTED = 2;  // 落選
    private const SELECTED     = 3;  // 当選

    /**
     * main() method.
     *
     * @param string $campaign_key
     * @param int $retweets_id
     * @return bool|int|null Success or error code.
     */
    public function main($campaign_key = null, $retweets_id = null)
    {
        if ($this->setArgs($campaign_key, $retweets_id) === false) {
            return false;
        }
        try {
            $this->lock($this->campaign_key);
            $this->api = $this->twitter_open();
            $retweet = $this->getRetweet();
            $this->setting = $this->getSettings($retweet['campaign_key']);
            if ($retweet === false) {
                return false;
            }
            $winner_flag = $this->playLottery($retweet);
            $winner_flag = self::SELECTED;
            switch ($winner_flag) {
                case self::NOT_COVERED:
                    $this->info('判定結果: 対象外');
                    break;
                case self::NOT_SELECTED:
                    $this->info('判定結果: 落選');
                    $media_id_string = $this->uploadMedia(
                        self::MOVIE_DIR . $this->setting['rejected_movie']
                    );
                    if ($media_id_string === false) {
                        return false;
                    }
                    $result = $this->statusUpdate([
                        'status'    => sprintf('@%s %s', $retweet->name, $this->setting['rejected_message']),
                        'media_ids' => $media_id_string
                    ]);
                    if ($result === false) {
                        return false;
                    }
                    $retweet->mention_send_flag = 1;
                    break;
                case self::SELECTED:
                    $this->info('判定結果: 当選');
                    $media_id_string = $this->uploadMedia(
                        self::MOVIE_DIR . $this->setting['winning_movie']
                    );
                    if ($media_id_string === false) {
                        return false;
                    }
                    $result = $this->statusUpdate([
                        'status'    => sprintf('@%s %s', $retweet->name, $this->setting['winning_message']),
                        'media_ids' => $media_id_string
                    ]);
                    if ($result === false) {
                        return false;
                    }
                    $t_winners = TableRegistry::get('Winners');
                    $winner = $t_winners->newEntity();
                    $winner->official_account_key = $retweet->official_account_key;
                    $winner->campaign_key = $this->campaign_key;
                    $winner->twitter_user_id = $retweet->twitter_user_id;
                    $winner->dm_send_flag = 0;
                    $t_winners->save($winner);
                    $retweet->mention_send_flag = 1;
                    break;
            }
            $t_retweets = TableRegistry::get('Retweets');
            $retweet->winner_flag = $winner_flag;
            $t_retweets->save($retweet);
        } finally {
            $this->twitter_close();
            $this->unlock();
        }
        $this->success('----- Finished.');
        return true;
    }

    /**
     * @return bool true(success)|false(failed)
     */
    private function setArgs($campaign_key = null, $retweets_id = null)
    {
        if (is_null($campaign_key)) {
            $this->log_error('第1引数(キャンペーン識別キー)がありません');
            return false;
        }
        $this->campaign_key = (string)$campaign_key;
        $this->info(
            sprintf('[args.1] campaign_key = %s', $this->campaign_key)
        );
        if (is_null($retweets_id)) {
            $this->log_error('第2引数(リツイートID)がありません');
            return false;
        }
        $this->retweets_id = (int)$retweets_id;
        $this->info(
            sprintf('[args.2] retweets_id  = %d', $this->retweets_id)
        );
        return true;
    }

    /**
     * @param string $path アップロードするファイルのパス
     * @return false|string media_id_string or false
     */
    private function uploadMedia($path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $media_type = finfo_file($finfo, $path);
        finfo_close($finfo);

        $options = [
            'media' => $path,
            'media_type' => $media_type
        ];
        $result = $this->api->upload('media/upload', $options, true);
        if (property_exists($result, 'errors')) {
            foreach ($result->errors as $r) {
                $this->log_error(sprintf(
                    '[uploadMedia] %s (error_code:%d)',
                    $r->message,
                    $r->code
                ));
            }
            return false;
        }
        return $result->media_id_string;
    }

    /**
     * @param array $params ツイートAPIに渡すパラメータ
     * @return false|object
     */
    private function statusUpdate($params)
    {
        $result = $this->twitter_api('post', 'statuses/update', $params);
        if (property_exists($result, 'errors')) {
            foreach ($result->errors as $r) {
                $this->log_error(sprintf(
                    '[statusUpdate] %s (error_code:%d)',
                    $r->message,
                    $r->code
                ));
            }
            return false;
        }
        return $result;
    }

    /**
     * @return array Retweetsテーブルのレコード
     */
    private function getRetweet()
    {
        $retweet = TableRegistry::get('Retweets')->find()->where([
            'id'           => $this->retweets_id,
            'campaign_key' => $this->campaign_key,
            'winner_flag'  => self::UNTREATED
        ])->first();
        if (is_null($retweet)) {
            $this->log_error(sprintf(
                '処理対象retweets(id=%d, winner_flag=%d)が見つかりませんでした',
                $this->retweets_id,
                self::UNTREATED
            ));
            return false;
        }
        return $retweet;
    }

    /**
     * @param string $campaign_key キャンペーン識別キー
     */
    private function getSettings($campaign_key)
    {
        return Configure::read(
            sprintf('campaigns.%s', $campaign_key)
        );
    }

    /**
     * 引数のリツイート情報が「対象外」か判定する
     *
     * @param array Retweetsテーブルのレコード
     * @return bool
     */
    private function isNotCovered($retweet)
    {
        if (is_null(@$this->setting['enabled']) or $this->setting['enabled'] === false) {
            $this->info('キャンペーンが無効です');
            return true;
        }

        $tweet_time = strtotime((string)$retweet['tweet_created_at']);
        if (
            $tweet_time < strtotime($this->setting['start_at'])
            or strtotime($this->setting['end_at']) < $tweet_time
        ) {
            $this->info('リツイートが有効期間外です');
            return true;
        }

        if (in_array($retweet['twitter_user_id'], $this->setting['black_list'])) {
            $this->info('ブラックリストに含まれています');
            return true;
        }

        $followers = TableRegistry::get('Followers');
        $query = $followers->find();
        $query->where(['twitter_user_id' => $retweet['twitter_user_id']]);
        if (is_null($query->first())) {
            $this->info('フォロワーではありません');
            return true;
        }

        $t = TableRegistry::get('Retweets');
        $query = $t->find();
        $query->where([
            'campaign_key'          => $retweet['campaign_key'],
            'twitter_user_id'       => $retweet['twitter_user_id'],
            'tweet_created_at LIKE' => date('Y-m-d%', time()),
            'winner_flag IS NOT'    => self::UNTREATED
        ]);
        if (!is_null($query->first())) {
            $this->info('すでに本日の当落判定をおこなったユーザーです');
            return true;
        }

        return false;
    }

    /**
     * @param array Retweetsテーブルのレコード
     * @return int 抽選結果(1:対象外, 2:落選, 3:当選)
     */
    private function playLottery($retweet)
    {
        if ($this->isNotCovered($retweet)) {
            return self::NOT_COVERED;
        }

        $winners = TableRegistry::get('Winners');
        $query = $winners->find();
        $query->where([
            'campaign_key'    => $retweet['campaign_key'],
            'twitter_user_id' => $retweet['twitter_user_id']
        ]);
        if (!is_null($query->first())) {
            $this->info('すでに当選済みのユーザーです');
            return self::NOT_SELECTED;
        }

        $winners = TableRegistry::get('Winners');
        $query = $winners->find();
        $query->where(['campaign_key' => $retweet['campaign_key']]);
        if ($this->setting['winners_max'] <= $query->count()) {
            $this->info('キャンペーンの上限当選数に達しています');
            return self::NOT_SELECTED;
        }

        if ($retweet['protected'] == 1) {
            $this->info('鍵アカウントのユーザーです');
            return self::NOT_SELECTED;
        }

        if (strpos($retweet['name'], 'bot') !== false) {
            $this->info('nameに「bot」が含まれています');
            return self::NOT_SELECTED;
        }

        if ($retweet['followers_count'] < $this->setting['exclude_followers_min']) {
            $this->info('フォロワー数が既定値未満です');
            return self::NOT_SELECTED;
        }

        if ($retweet['statuses_count'] < $this->setting['exclude_tweets_min']) {
            $this->info('ツイート数が規定値未満です');
            return self::NOT_SELECTED;
        }

        $create_time = strtotime((string)$retweet['tweet_created_at']);
        $exclude_secs_count_min = 3600 * 24 * $this->setting['exclude_days_count_min'];
        if (time() - $create_time < $exclude_secs_count_min) {
            $this->info('規定日より新しいアカウントです');
            return self::NOT_SELECTED;
        }

        if (strpos($retweet['description'], '懸賞') !== false) {
            $this->info('プロフィールに「懸賞」が含まれています');
            return self::NOT_SELECTED;
        }

        $winners = TableRegistry::get('Winners');
        $query = $winners->find();
        $query->where([
            'campaign_key' => $retweet['campaign_key'],
            'created LIKE' => date('Y-m-d%', time()),
        ]);
        if ($this->setting['winners_daily_max'] <= $query->count()) {
            $this->info('当日の当選者数が上限に達しています');
            return self::NOT_SELECTED;
        }

        // 確率で抽選
        $threshold = (int)($this->setting['winning_rate'] * 100);
        $result    = mt_rand(1, 10000);
        $this->info(sprintf('抽選: %d / %d', $result, $threshold));
        if ($result <= $threshold) {
            return self::SELECTED;
        } else {
            return self::NOT_SELECTED;
        }
    }
}
