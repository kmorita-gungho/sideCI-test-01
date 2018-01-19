<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Winner Entity
 *
 * @property int $id
 * @property string $official_account_key
 * @property string $campaign_key
 * @property string $twitter_user_id
 * @property string $winning_numbers
 * @property int $dm_send_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\TwitterUser $twitter_user
 */
class Winner extends Entity
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
        'twitter_user_id' => true,
        'winning_numbers' => true,
        'dm_send_flag' => true,
        'created' => true,
        'modified' => true,
//        'twitter_user' => true
    ];
}
