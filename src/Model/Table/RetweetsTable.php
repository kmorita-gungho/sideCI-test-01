<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Retweets Model
 *
 * @property \App\Model\Table\TweetsTable|\Cake\ORM\Association\BelongsTo $Tweets
 * @property \App\Model\Table\TwitterUsersTable|\Cake\ORM\Association\BelongsTo $TwitterUsers
 *
 * @method \App\Model\Entity\Retweet get($primaryKey, $options = [])
 * @method \App\Model\Entity\Retweet newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Retweet[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Retweet|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Retweet patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Retweet[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Retweet findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RetweetsTable extends Table
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

        $this->setTable('retweets');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Tweets', [
//            'foreignKey' => 'tweet_id',
//            'joinType' => 'INNER'
//        ]);
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
            ->scalar('tweet_text')
            ->maxLength('tweet_text', 512)
            ->requirePresence('tweet_text', 'create')
            ->notEmpty('tweet_text');

        $validator
            ->dateTime('tweet_created_at')
            ->requirePresence('tweet_created_at', 'create')
            ->notEmpty('tweet_created_at');

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

        $validator
            ->integer('winner_flag')
            ->requirePresence('winner_flag', 'create')
            ->notEmpty('winner_flag');

        $validator
            ->integer('mention_send_flag')
            ->requirePresence('mention_send_flag', 'create')
            ->notEmpty('mention_send_flag');

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
//        $rules->add($rules->existsIn(['tweet_id'], 'Tweets'));
//        $rules->add($rules->existsIn(['twitter_user_id'], 'TwitterUsers'));

        return $rules;
    }
}
