<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Retweet Entity
 *
 * @property int $id
 * @property string $official_account_key
 * @property string $campaign_key
 * @property string $tweet_id
 * @property string $tweet_text
 * @property \Cake\I18n\FrozenTime $tweet_created_at
 * @property string $twitter_user_id
 * @property string $name
 * @property string $screen_name
 * @property string $description
 * @property int $protected
 * @property int $followers_count
 * @property int $friends_count
 * @property \Cake\I18n\FrozenTime $twitter_user_created_at
 * @property int $statuses_count
 * @property string $lang
 * @property int $winner_flag
 * @property int $mention_send_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Tweet $tweet
 * @property \App\Model\Entity\TwitterUser $twitter_user
 */
class Retweet extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'official_account_key' => true,
        'campaign_key' => true,
        'tweet_id' => true,
        'tweet_text' => true,
        'tweet_created_at' => true,
        'twitter_user_id' => true,
        'name' => true,
        'screen_name' => true,
        'description' => true,
        'protected' => true,
        'followers_count' => true,
        'friends_count' => true,
        'twitter_user_created_at' => true,
        'statuses_count' => true,
        'lang' => true,
        'winner_flag' => true,
        'mention_send_flag' => true,
        'created' => true,
        'modified' => true,
//        'tweet' => true,
//        'twitter_user' => true
    ];
}
