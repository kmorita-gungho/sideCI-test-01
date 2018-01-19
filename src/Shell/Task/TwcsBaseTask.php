<?php
namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Twcs base task.
 */
abstract class TwcsBaseTask extends Shell
{
    // ロックファイル作成ディレクトリ
    private const LOCK_DIR = TMP . 'lock' . DS;

    // 排他制御のファイルポインタ
    private $lock_fp;

    // データベースの接続ハンドル
    private $database_ch;

    // ツイッター API の接続ハンドル
    private $twitter_ch;

    /**
     * Initializes the Shell acts as constructor for subclasses allows configuration of tasks prior to shell execution
     * {@inheritDoc}
     * @see \Cake\Console\Shell::initialize()
     */
    public function initialize() {
        parent::initialize();

        $this->database_ch = ConnectionManager::get('default');
    }

    /**
     * データベースの接続ハンドル
     * @return object
     */
    protected function database_handle() {
        return $this->database_ch;
    }

    /**
     * 排他制御の開始
     * すでにロックされている場合はプログラムを終了する。
     * @param string $key
     * @return void
     */
    protected function lock(string $key) {
        $lock_file = "{$this->name}.{$key}.lock";
        $this->lock_fp = fopen(self::LOCK_DIR . $lock_file, 'a+');
        if (!flock($this->lock_fp, LOCK_EX | LOCK_NB)) {
            $this->log_error('排他制御が開始できません、すでにロックされています。');
            exit(1);
        }
    }

    /**
     * 排他制御の終了
     * ロックの解放は必須ではない、プログラム終了時には自動的に解放される。
     * @return void
     */
    protected function unlock() {
        fclose($this->lock_fp);
    }

    /**
     * エラー出力
     * @param string $msg
     * @return void
     */
    protected function log_error(string $msg) {
        // ログ出力
        $this->log("{$this->name}: {$msg}", E_ERROR);

        // メール送信
        $recipients = Configure::read('account.error_mail_recipient');
        $this->email($recipients, [
            'subject' => "Twitterキャンペーン用システム: {$this->name}: エラー",
            'message' => $msg,
        ]);
    }

    /**
     * メール送信
     * @param string/array $recipients
     * @param array $params
     * @return void
     */
    protected function email($recipients, array $params) {
        if (!is_array($recipients)) $recipients = array($recipients);

        foreach ($recipients as $recipient) {
            $email = new Email('default');
            $email->from(Configure::read('account.mail_from'))
            ->to($recipient)
            ->subject($params['subject'])
            ->send($params['message']);
        }
    }

    /**
     * ツイッター API に接続
     * @return object
     */
    protected function twitter_open() {
        $consumer_key        = Configure::read('account.consumer_key');
        $consumer_secret     = Configure::read('account.consumer_secret');
        $access_token        = Configure::read('account.access_token');
        $access_token_secret = Configure::read('account.access_token_secret');

        $this->twitter_ch = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        return $this->twitter_ch;
    }

    /**
     * ツイッター API から切断
     * 切断は必須ではない、プログラム終了時には自動的に切断される。
     * @return void
     */
    protected function twitter_close() {
        $this->twitter_ch = null;
    }

    /**
     * ツイッター API の接続ハンドル
     * @return object
     */
    protected function twitter_handle() {
        return $this->twitter_ch;
    }

    /**
     * ツイッター API の実行
     * @param string $method get/post
     * @param string $api
     * @param array $params
     * @return object
     */
    protected function twitter_api(string $method, string $api, array $params) {
        $result = $this->twitter_ch->$method($api, $params);
        return $result;
    }
}
