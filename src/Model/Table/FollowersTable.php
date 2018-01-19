<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Followers Model
 *
 * @property \App\Model\Table\TwitterUsersTable|\Cake\ORM\Association\BelongsTo $TwitterUsers
 *
 * @method \App\Model\Entity\Follower get($primaryKey, $options = [])
 * @method \App\Model\Entity\Follower newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Follower[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Follower|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Follower patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Follower[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Follower findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FollowersTable extends Table
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

        $this->setTable('followers');
        $this->setDisplayField('name');
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
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('screen_name')
            ->maxLength('screen_name', 128)
            ->requirePresence('screen_name', 'create')
            ->notEmpty('screen_name');

        $validator
            ->scalar('description')
            ->maxLength('description', 256)
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->integer('protected')
            ->requirePresence('protected', 'create')
            ->notEmpty('protected');

        $validator
            ->integer('followers_count')
            ->requirePresence('followers_count', 'create')
            ->notEmpty('followers_count');

        $validator
            ->integer('friends_count')
            ->requirePresence('friends_count', 'create')
            ->notEmpty('friends_count');

        $validator
            ->dateTime('twitter_user_created_at')
            ->requirePresence('twitter_user_created_at', 'create')
            ->notEmpty('twitter_user_created_at');

        $validator
            ->integer('statuses_count')
            ->requirePresence('statuses_count', 'create')
            ->notEmpty('statuses_count');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 64)
            ->allowEmpty('lang');

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
