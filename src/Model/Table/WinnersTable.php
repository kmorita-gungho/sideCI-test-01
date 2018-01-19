<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Winners Model
 *
 * @property \App\Model\Table\TwitterUsersTable|\Cake\ORM\Association\BelongsTo $TwitterUsers
 *
 * @method \App\Model\Entity\Winner get($primaryKey, $options = [])
 * @method \App\Model\Entity\Winner newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Winner[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Winner|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Winner patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Winner[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Winner findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WinnersTable extends Table
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

        $this->setTable('winners');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('TwitterUsers', [
//            'foreignKey' => 'twitter_user_id',
//            'joinType' => 'INNER'
//        ]);
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
            ->scalar('winning_numbers')
            ->maxLength('winning_numbers', 128)
            ->allowEmpty('winning_numbers');

        $validator
            ->integer('dm_send_flag')
            ->requirePresence('dm_send_flag', 'create')
            ->notEmpty('dm_send_flag');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
//        $rules->add($rules->existsIn(['twitter_user_id'], 'TwitterUsers'));

        return $rules;
    }
}
