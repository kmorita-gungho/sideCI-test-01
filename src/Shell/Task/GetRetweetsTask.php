<?php
namespace App\Shell\Task;

use Cake\Core\Configure;
use Cake\Validation\Validator;
use Cake\Console\Exception\ConsoleException;

/**
 * GetRetweets shell task.
 */
class GetRetweetsTask extends TwcsBaseTask
{
    /**
     * Initializes the Shell acts as constructor for subclasses allows configuration of tasks prior to shell execution
     * {@inheritDoc}
     * @see \Cake\Console\Shell::initialize()
     */
    public function initialize() {
        parent::initialize();

        $this->loadModel('LastRetweets');
        $this->loadModel('Retweets');
    }

    /**
     * Gets the option parser instance and configures it.
     * {@inheritDoc}
     * @see \Cake\Console\Shell::getOptionParser()
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        // 引数の定義
        $parser->addArguments([
            'campaign_key' => ['index' => 0, 'help' => 'キャンペーン識別キー', 'required' => true ],
        ]);
        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        try {
            // 引数のバリデーション・ルール
            $validator = new Validator();
            $validator
                ->scalar('campaign_key')->maxLength('campaign_key', 128)
                ->add('campaign_key', 'custom', [
                    'rule' => function ($value) {
                        return !empty(Configure::read("campaigns.{$value}")); // 有効なキャンペーン識別キーか？
                    },
                    'message' => 'Unknown campaign'
                ])
                ;
            $args = [
                'campaign_key' => $this->args[0],
            ];

            // 引数のバリデーション
            $errors = $validator->errors($args);
            foreach ($errors as $name => $error) {
                $error = implode(', ', $error);
                throw new ConsoleException("{$name}: {$error}");
            }

            // 引数を内部値に変換
            $this->args = [
                'campaign_key' => $args['campaign_key'],
            ];

        } catch (ConsoleException $e) {
            $this->err('Error: ' . $e->getMessage());
            return false;
        }

//$this->database_handle()->logQueries(true); // SQL dump

        try {
            $this->lock($this->args['campaign_key']);
            $this->twitter_open();

            // 第１ブロック： リツイートの一覧を取得
            $this->block1();

            // 第２ブロック： 「当落メンション送信プログラム」の実行
            $this->block2();

        } finally {
            $this->twitter_close();
            $this->unlock();
        }
    }

    /**
     * 第１ブロック： リツイートの一覧を取得
     * @return void
     */
    private function block1() {
        $campaign = Configure::read("campaigns.{$this->args['campaign_key']}");

        $q           = ltrim($campaign['hash_tag'], '#'); // ハッシュタグのテキスト部分
        $screen_name = Configure::read('account.screen_name'); // 公式アカウントの screen_name
        $since_id    = '0';
        $max_id      = '';

        // 最終リツイート情報の取得
        $last_retweet = $this->getLastRetweet();
        if (!empty($last_retweet)) {
            // 最終リツイート情報があるなら
            if (!empty($last_retweet->since_id)) $since_id = $last_retweet->since_id;
            if (!empty($last_retweet->max_id))   $max_id   = $last_retweet->max_id;
        } else {
            // 最終リツイート情報がないなら、新しいレコードを保存する
            $last_retweet = $this->LastRetweets->newEntity();
            $last_retweet->official_account_key = Configure::read('account.key'); // 公式アカウントキー
            $last_retweet->campaign_key         = $this->args['campaign_key'];    // キャンペーンキー

            // 暫定対応: tweet_id を "since_id,max_id" として使う
            list($last_retweet->since_id, $last_retweet->max_id) = [$since_id, $max_id];
            $last_retweet->tweet_id = implode(',', [$since_id, $max_id]);

            $this->LastRetweets->save($last_retweet);
        }

        do {
            // リツイートの一覧取得
            $retweets = $this->searchRetweets($q, $screen_name, $max_id, $since_id);
            if (!empty($retweets)) {
                // リツイート情報の保存
                $max_id = $this->saveRetweets($retweets, $last_retweet);

                // max_id を更新する
                // 暫定対応: tweet_id を "since_id,max_id" として使う
                list($last_retweet->since_id, $last_retweet->max_id) = [$since_id, $max_id];
                $last_retweet->tweet_id = implode(',', [$since_id, $max_id]);

                $this->LastRetweets->save($last_retweet);
            }
        } while (!empty($retweets));

        if (empty($retweets) && is_array($retweets)) {
            // 取得していた範囲を取りきったなら since_id を更新する

            // 最大のリツイートID を取得
            $max_tweet_id = $this->getMaxRetweetID();

            // 暫定対応: tweet_id を "since_id,max_id" として使う
            list($last_retweet->since_id, $last_retweet->max_id) = [$max_tweet_id, ''];
            $last_retweet->tweet_id = implode(',', [$max_tweet_id]);

            $this->LastRetweets->save($last_retweet);
        }
    }

    /**
     * 第２ブロック： 「当落メンション送信プログラム」の実行
     * @return void
     */
    private function block2() {
        $campaign = Configure::read("campaigns.{$this->args['campaign_key']}");

        $retweets = $this->getUndecidedRetweet();
        foreach ($retweets as $retweet) {
            // 「当落メンション送信プログラム」を実行
            $this->dispatchShell("twcs SendResultMention {$this->args['campaign_key']} {$retweet->tweet_id}");

            sleep($campaign['dicision_interval']);
        }
    }

    /**
     * リツイートの一覧取得
     * @param string $q
     * @param string $screen_name
     * @param string $max_id
     * @param string $since_id
     * @return object/false retweets
     */
    private function searchRetweets(string $q, string $screen_name, string $max_id, string $since_id) {
        $q = "{$q} filter:retweets @{$screen_name}";
        $params = [
            'q'           => $q,
            'result_type' => 'mixed',
            'count'       => 100,
            'since_id'    => $since_id,
        ];
        if (!empty($max_id)) $params['max_id'] = $max_id;
        $result = $this->twitter_api('get', 'search/tweets', $params);

        if (isset($result->errors)) {
            // 取得失敗 （88:Rate limit exceeded）
            $this->log_error("{$this->args['campaign_key']}: リツイートの取得失敗 '{$result->errors[0]->message}'");
            return false;
        }

        return (isset($result->statuses) ? $result->statuses : false);
    }

    /**
     * 最終リツイート情報の取得
     * @return object
     */
    private function getLastRetweet() {
        $conditions = [
            'campaign_key' => $this->args['campaign_key'],
        ];

        $retweet = $this->LastRetweets->find('all', ['conditions' => $conditions])->first();
        if (!empty($retweet)) {
            // 暫定対応: tweet_id を "since_id,max_id" として使う
            $a = explode(',', $retweet->tweet_id, 2);
            list($retweet->since_id, $retweet->max_id) = [$a[0], @$a[1]];
        }
        return $retweet;
    }

    /**
     * リツイート情報の保存
     * @param iterable $retweets
     * @param object/null $last_retweet
     * @return string minimum tweet_id
     */
    private function saveRetweets(iterable $retweets, $last_retweet) {
        $max_id = '';
        foreach ($retweets as $retweet) {
            $tweet_id = $retweet->id_str;
            if (empty($max_id) || ($max_id > $tweet_id)) $max_id = $tweet_id; // 最小ツイートID

            $conditions = [
                'campaign_key' => $this->args['campaign_key'],
                'tweet_id'     => $tweet_id
            ];

            $row = $this->Retweets->find('all', ['conditions' => $conditions])->first();
            if (!empty($row)) continue; // すでに保存済みならスキップ

            // リツイート情報の保存
            $row = $this->Retweets->newEntity();
            $row->official_account_key = Configure::read('account.key'); // 公式アカウントキー
            $row->campaign_key         = $this->args['campaign_key'];    // キャンペーンキー
            $row->winner_flag          = 0; // 当選者フラグ
            $row->mention_send_flag    = 0; // 当落通知メンション送信フラグ
            {
                // 取得したリツイート情報
                $row->tweet_id           = $tweet_id;            // ツイートID
                $row->tweet_text         = $retweet->text;       // ツイート内容
                $row->tweet_created_at   = $retweet->created_at; // ツイート作成日

                $row->twitter_user_id    = $retweet->user->id_str;          // twitterユーザID
                $row->name               = $retweet->user->name;            // twitter名
                $row->screen_name        = $retweet->user->screen_name;     // twitterスクリーン名
                $row->description        = $retweet->user->description;     // プロフィール
                $row->protected          = $retweet->user->protected;       // 鍵フラグ
                $row->followers_count    = $retweet->user->followers_count; // フォロワー数
                $row->friends_count      = $retweet->user->friends_count;   // フレンド数
                $row->twitter_user_created_at = $retweet->user->created_at; // twitter登録日
                $row->statuses_count     = $retweet->user->statuses_count;  // ツイート数
                $row->lang               = $retweet->user->lang;            // 言語

                $row->tweet_created_at        = strtotime($row->tweet_created_at);
                $row->twitter_user_created_at = strtotime($row->twitter_user_created_at);
            }
            $this->Retweets->save($row);
        }

        // $max_id を数値文字列としてデクリメント（-1）する
        for ($i = strlen($max_id) - 1; $i >= 0; $i--) {
            $c = chr(ord($max_id[$i]) - 1);
            if ($c >= '0') {
                $max_id[$i] = $c;
                break;
            }

            $max_id[$i] = ($i > 0 ? '9' : '');
        }
        $max_id = ltrim($max_id, '0');

        return $max_id;
    }

    /**
     * 最大のリツイートID を取得
     * @return string
     */
    private function getMaxRetweetID() {
        $conditions = [
            'campaign_key' => $this->args['campaign_key'],
        ];

        $row = $this->Retweets->find('all', ['conditions' => $conditions]);
        $row = $row->select(['max_tweet_id' => $row->func()->max('tweet_id')])->first();
        return (!empty($row) ? $row->max_tweet_id : '');
    }

    /**
     * 当落判定が未処理のリツイートを取得
     * @return object
     */
    private function getUndecidedRetweet() {
        $conditions = [
            'campaign_key' => $this->args['campaign_key'],
            'winner_flag'  => 0 // 未処理
        ];

        $retweets = $this->Retweets->find('all', ['conditions' => $conditions, 'order' => 'id']);
        return $retweets;
    }
}
