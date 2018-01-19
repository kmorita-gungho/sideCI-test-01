<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * WinningNumbers Model
 *
 * @method \App\Model\Entity\WinningNumber get($primaryKey, $options = [])
 * @method \App\Model\Entity\WinningNumber newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\WinningNumber[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\WinningNumber|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\WinningNumber patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\WinningNumber[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\WinningNumber findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WinningNumbersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('winning_numbers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('official_account_key')
            ->maxLength('official_account_key', 128)
            ->requirePresence('official_account_key', 'create')
            ->notEmpty('official_account_key');

        $validator
            ->scalar('campaign_key')
            ->maxLength('campaign_key', 128)
            ->requirePresence('campaign_key', 'create')
            ->notEmpty('campaign_key');

        $validator
            ->scalar('numbers')
            ->maxLength('numbers', 128)
            ->requirePresence('numbers', 'create')
            ->notEmpty('numbers');

        $validator
            ->integer('used_flag')
            ->requirePresence('used_flag', 'create')
            ->notEmpty('used_flag');

        return $validator;
    }
}
