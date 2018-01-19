<?php
namespace App\Shell\Task;

use Cake\Core\Configure;
use Cake\Validation\Validator;
use Cake\Console\Exception\ConsoleException;

/**
 * SendWinnerDm shell task.
 */
class SendWinnerDmTask extends TwcsBaseTask
{
    /**
     * Initializes the Shell acts as constructor for subclasses allows configuration of tasks prior to shell execution
     * {@inheritDoc}
     * @see \Cake\Console\Shell::initialize()
     */
    public function initialize() {
        parent::initialize();

        $this->loadModel('Winners');
        $this->loadModel('WinningNumbers');
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
            'start_date'   => ['index' => 1, 'help' => '配信 開始年月日',   'required' => false],
            'end_date'     => ['index' => 2, 'help' => '配信 終了年月日',   'required' => false]
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
                ->date('start_date')->allowEmpty('start_date')
                ->date('end_date')->allowEmpty('end_date')
                ;
            $args = [
                'campaign_key' => $this->args[0],
                'start_date'   => @$this->args[1], // required: false
                'end_date'     => @$this->args[2]  // required: false
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
                'start_date'   => strtotime($args['start_date']),
                'end_date'     => strtotime($args['end_date']),
            ];

        } catch (ConsoleException $e) {
            $this->err('Error: ' . $e->getMessage());
            return false;
        }

//$this->database_handle()->logQueries(true); // SQL dump

        try {
            $this->lock($this->args['campaign_key']);
            $this->twitter_open();

            $campaign = Configure::read("campaigns.{$this->args['campaign_key']}");

            // 送信対象の当選者を取得する
            $winners = $this->getWinners();

            $cnt_total = $this->getTotalWinnersCount(); // 当選者総数
            $cnt_send  = $winners->count();             // 配信先総数
            $cnt_ok = 0; // 送信成功
            $cnt_ng = 0; // 送信失敗
            $dm_log = [];
            foreach ($winners as $row) {
                $this->database_handle()->begin();

                // 当選番号の取得
                $number = $this->getWinningNumber();
                if (empty($number)) {
                    // 取得失敗
                    $this->database_handle()->rollback();
                    $cnt_ng++;
                    $msg = '失敗';
                    $this->log_error("{$this->args['campaign_key']}: 当選番号の取得失敗");
                } else {
                    // 取得成功

                    // 当選ダイレクトメッセージの送信
                    $errors = $this->directMessage($row->twitter_user_id, $number);
                    if ($errors === false) {
                        // 送信成功
                        $row->winning_numbers = $number;
                        $row->dm_send_flag = 1; // DM 送信済み
                        $this->Winners->save($row);
                        $this->database_handle()->commit();
                        $cnt_ok++;
                        $msg = '成功';
                    } else {
                        // 送信失敗
                        $this->database_handle()->rollback();
                        $cnt_ng++;
                        $msg = '失敗';
                        $this->log_error("{$row->twitter_user_id}: ダイレクトメッセージの送信失敗");
                    }
                }
                $dm_log[] = "{$row->twitter_user_id} : {$msg}";

                sleep($campaign['winning_dm_interval']);
            }

            // 管理者にダイレクトメッセージの送信結果をメール
            $params = [
                'cnt_total' => $cnt_total, // 当選者総数
                'cnt_send'  => $cnt_send,  // 配信先総数
                'cnt_ok'    => $cnt_ok,    // 送信成功
                'cnt_ng'    => $cnt_ng,    // 送信失敗
                'dm_log'    => $dm_log     // 送信ログ
            ];
            $this->completeEmail($params);

        } finally {
            $this->twitter_close();
            $this->unlock();
        }
    }

    /**
     * 当選番号の取得
     * @return string/false
     */
    private function getWinningNumber() {
        $conditions = [
            'campaign_key' => $this->args['campaign_key'],
            'used_flag'    => 0 // 未使用
        ];
        $numbers = false;

        // 当選番号を取得してから、使用済みフラグをセットする （SELECT FOR UPDATE）
        $this->database_handle()->begin();
        {
            $row = $this->WinningNumbers->find('all', ['conditions' => $conditions, 'order' => 'id', 'limit' => 1])->epilog('FOR UPDATE')->first();
            if (!empty($row)) {
                $numbers = $row->numbers;
                $row->used_flag = 1; // 使用済み
                $this->WinningNumbers->save($row);
            }
        }
        $this->database_handle()->commit();

        return $numbers;
    }

    /**
     * 当選者総数を取得する
     * @return int
     */
    private function getTotalWinnersCount() {
        $conditions = [
            'campaign_key' => $this->args['campaign_key'],
        ];

        $count = $this->Winners->find('all', ['conditions' => $conditions])->count();
        return $count;
    }

    /**
     * 配信対象の当選者を取得する
     * @return object
     */
    private function getWinners() {
        $conditions = [
            'campaign_key' => $this->args['campaign_key'],
            'dm_send_flag' => 0  // 当選番号 未送信
        ];

        if (!empty($this->args['start_date']))
            $conditions['created >='] = date('Y-m-d 00:00:00', $this->args['start_date']); // 配信 開始年月日
        if (!empty($this->args['end_date']))
            $conditions['created <='] = date('Y-m-d 23:59:59', $this->args['end_date']);   // 配信 終了年月日

        $winners = $this->Winners->find('all', ['conditions' => $conditions, 'order' => 'id']);
        return $winners;
    }

    /**
     * 当選ダイレクトメッセージの送信
     * @param string $user_id
     * @param string $number
     * @return object/false errors
     */
    private function directMessage(string $user_id, string $number) {
        $campaign = Configure::read("campaigns.{$this->args['campaign_key']}");
        $result = $this->twitter_api('post', 'direct_messages/new', [
            'user_id' => $user_id,
            'text'    => sprintf($campaign['winning_dm_message'], $number)
        ]);
        return (isset($result->errors) ? $result->errors[0] : false);
    }

    /**
     * 管理者に送信結果をメール
     * @param array $params
     * @return void
     */
    private function completeEmail(array $params) {
        $campaign = Configure::read("campaigns.{$this->args['campaign_key']}");

        $start_date = '';
        if (!empty($this->args['start_date']))
            $start_date = date('Y-m-d', $this->args['start_date']); // 配信 開始年月日
        $end_date = '';
        if (!empty($this->args['end_date']))
            $end_date = date('Y-m-d', $this->args['end_date']);     // 配信 終了年月日

        $log = implode("\n", $params['dm_log']);
        if (!empty($log)) {
            $log .= "\n\n以上。";
        } else {
            $log .= 'なし';
        }

        $params = [
            'subject' => "[TWCS {$this->args['campaign_key']}] 当選番号DM送信報告",
            'message' => <<<_MSG_
Twitterキャンペーン {$this->args['campaign_key']} の当選番号DM送信が完了しました。

配信範囲開始年月日： {$start_date}
配信範囲終了年月日： {$end_date}

当選者総数： {$params['cnt_total']}
配信先総数： {$params['cnt_send']}
送信成功　： {$params['cnt_ok']}
送信失敗　： {$params['cnt_ng']}

[送信ログ]

{$log}
_MSG_
        ];
        $this->email($campaign['dm_complete_email_recipient'], $params);
    }
}
