<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LastRetweet Entity
 *
 * @property int $id
 * @property string $official_account_key
 * @property string $campaign_key
 * @property string $tweet_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Tweet $tweet
 */
class LastRetweet extends Entity
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
        'created' => true,
        'modified' => true,
//        'tweet' => true
    ];
}
