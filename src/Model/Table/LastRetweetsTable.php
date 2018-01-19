<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LastRetweets Model
 *
 * @property \App\Model\Table\TweetsTable|\Cake\ORM\Association\BelongsTo $Tweets
 *
 * @method \App\Model\Entity\LastRetweet get($primaryKey, $options = [])
 * @method \App\Model\Entity\LastRetweet newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LastRetweet[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LastRetweet|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LastRetweet patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LastRetweet[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LastRetweet findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LastRetweetsTable extends Table
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

        $this->setTable('last_retweets');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Tweets', [
//            'foreignKey' => 'tweet_id',
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

        return $rules;
    }
}
