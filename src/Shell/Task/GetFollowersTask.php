<?php
namespace App\Shell\Task;

use Cake\Core\Configure;
use Cake\I18n\Time;

/**
 * GetFollowers shell task.
 */
class GetFollowersTask extends TwcsBaseTask
{

    /**
     * Initializes the Shell acts as constructor for subclasses allows configuration of tasks prior to shell execution
     * {@inheritDoc}
     * @see \Cake\Console\Shell::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Cursors');
        $this->loadModel('Followers');
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->account_key = Configure::read('account.key');
        try {
            $this->lock($this->account_key);
            // Twitter REST API処理
            $this->twitter_open();
            
            $counter = 0;
            do {
                $counter ++;
                //$this->out($counter . '回目');
                
                // カーソル取得
                $cursor = $this->getCursor();
                
                // フォロワーリスト取得
                $result = $this->twitter_api('GET', 'followers/list', [
                    //'count' => 1,
                    'count' => 200,
                    'cursor' => (($cursor->next_cursor === '0') ? '-1' : $cursor->next_cursor),
                ]);
                print_r($result);
                if (isset($result->errors)) {
                    // 取得失敗 （88:Rate limit exceeded）
                    $this->log_error("{$this->account_key}: フォロワーの取得失敗 '{$result->errors[0]->message}'");
                    exit(1);
                }
                
                // フォロワーリストを保存する。
                if (! $this->saveFollowers($result->users)) {
                    $this->log_error("{$this->account_key}:フォロワーリストの更新失敗");
                    exit(1);
                }

                // カーソル保存
                if (((string) $result->next_cursor) !== '0') {
                    $cursor->next_cursor = (string) $result->next_cursor;
                    $this->Cursors->connection()->logQueries(true);
                    if (! $this->Cursors->save($cursor)) {
                        $this->log_error("{$this->account_key}:カーソルの更新失敗");
                        exit(1);
                    }
                }
            } while (((string) $result->next_cursor) !== '0');
            
        } catch (\Exception $e) {
            $this->log_error('例外が発生しました。: ' . $e->getMessage());
        } finally {
            $this->twitter_close();
            $this->unlock();
        }

        $this->out('GetFollowersTask ok.');
    }

    /**
     * カーソルの取得
     * @return object
     */
    private function getCursor()
    {
        $cursor = $this->Cursors->find()
            ->where(['official_account_key' => $this->account_key])
            ->andWhere(['api_type' => 'GET followers/list'])
            ->first();
        if (! $cursor) {
            $cursor = $this->Cursors->newEntity([
                'official_account_key' => $this->account_key,
                'api_type' => 'GET followers/list',
                'next_cursor' => '-1',
            ]);
        }
        return $cursor;
    }

    /**
     * リツイート情報の保存
     * @param iterable $followers
     * @return boolean Success or Fail
     */
    private function saveFollowers(iterable $followers)
    {
        foreach ($followers as $follower) {
            $row = $this->Followers->find()
                ->where(['official_account_key' => $this->account_key])
                ->andWhere(['twitter_user_id' => $follower->id_str])
                ->first();
            if (! empty($row)) continue; // すでに保存済みならスキップ
            
            $row = $this->Followers->newEntity();
            $row->official_account_key      = $this->account_key;
            $row->twitter_user_id           = $follower->id_str;
            $row->name                      = $follower->name;
            $row->screen_name               = $follower->screen_name;
            $row->description               = $follower->description;
            $row->protected                 = (($follower->protected == '') or ($follower->protected == false)) ? 0 : 1;
            $row->followers_count           = ($follower->followers_count == '') ? 0 : $follower->followers_count;
            $row->friends_count             = ($follower->friends_count == '') ? 0 : $follower->friends_count;
            $row->twitter_user_created_at   = (new Time($follower->created_at))->timezone('Asia/Tokyo');
            $row->statuses_count            = ($follower->statuses_count == '') ? 0 : $follower->statuses_count;
            $row->lang                      = $follower->lang;
            if (! $this->Followers->save($row)) {
                $this->log('フォロワー保存時のエラー: ' . print_r($row->errors()), LOG_DEBUG);
                return false;
            }
        }
        return true;
    }

}
