<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cursor Entity
 *
 * @property int $id
 * @property string $official_account_key
 * @property string $api_type
 * @property string $next_cursor
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Cursor extends Entity
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
        'api_type' => true,
        'next_cursor' => true,
        'created' => true,
        'modified' => true
    ];
}
