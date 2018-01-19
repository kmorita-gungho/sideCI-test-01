<?php

return [

    /**
     * 公式アカウント設定
     */
    'account' => [
        // 公式アカウントを一意に識別するキー文字列（screen_nameでOK）
        'key'                   => 'ttst01t',
        // twitter id
        'id'                    => '939395033249210368',
        // twitter screen_name
        'screen_name'           => 'ttst01t',
        // twitter name
        'name'                  => 'ttst01ですだよ',
        // Twitter API consumer_key
        'consumer_key'          => '*********',
        // Twitter API consumer_secret
        'consumer_secret'       => '*********',
        // Twitter API access_token
        'access_token'          => '*********',
        // Twitter API access_token_secret
        'access_token_secret'   => '*********',
        // エラーメール送信先アドレス（配列で複数設定可能）
        'error_mail_recipient'  => ['hiro@aolab.jp', 'info@aolab.jp', ],
        // メール送信者アドレス
        'mail_from' => 'noreply@aolab.jp',
    ],

    /**
     * キャンペーン設定（配列）
     */
    'campaigns' => [
        // 1件目
        'campaign001' => [ // キャンペーンを一意に識別するキー文字列
            // キャンペーン名称
            'title' => '第1回〇〇〇キャンペーン',
            // キャンペーン対象ツイートのstatus id
            'status_id' => '944472581158998017',
            // キャンペーン用ハッシュタグ
            'hash_tag'  => '#〇〇キャンペーン',
            // キャンペーン有効期間 開始日時
            'start_at' => '2018-01-10 00:00:00',
            // キャンペーン有効期間 終了日時
            'end_at' => '2018-01-20 00:00:00',
            // キャンペーンの有効フラグ（true or false）
            'enabled' => true,
            // 当落判定間隔（秒）
            'dicision_interval' => 0,
            // キャンペーン対象外ユーザ（ブラックリスト）のID配列
            // screen_nameではなく、twitterのID（int64）で記載する
            'black_list' => [
                '939395033249210368',
                '939395033249210369',
            ],
            // 当選者数
            'winners_max' => 100,
            // 1日あたりの当選者数
            'winners_daily_max' => 10,
            // 当選確率（％で指定。小数点以下2桁まで指定可。例： 0.05％ = 0.0005）
            'winning_rate' => 0.05,
            // 対象除外条件：フォロワー数最小値（30件以下は除外）
            'exclude_followers_min' => 30,
            // 対象除外条件：ツイート数最小値（30件以下は除外）
            'exclude_tweets_min' => 30,
            // 対象除外条件：アカウント開設日からの経過日数（60日以下は除外）
            'exclude_days_count_min' => 30,
            // 当選した際のメンションメッセージ
            'winning_message' => "おめでとうございます！\n当選しました。",
            // 落選した際のメンションメッセージ
            'rejected_message' => "残念でした！\n落選しました。",
            // 当選動画ファイルのファイル名
            'winning_movie' => 'winner.mov',
            // 落選動画ファイルのファイル名
            'rejected_movie' => 'rejected.mov',
            // 当選者へのDMメッセージ
            'winning_dm_message' => "当選番号は %s です。\n手続きURLは下記です。\nhttp://example.com/hoge",
            // 当選者へのDM送信間隔（秒)
            'winning_dm_interval' => 0,
            // 当選者へのDM送信完了時メール送信先アドレス（配列で複数設定可能）
            'dm_complete_email_recipient' => ['hiro@aolab.jp', ],
        ],
    ],

];

