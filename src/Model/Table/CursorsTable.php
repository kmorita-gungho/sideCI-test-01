<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cursors Model
 *
 * @method \App\Model\Entity\Cursor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cursor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cursor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cursor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cursor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cursor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cursor findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CursorsTable extends Table
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

        $this->setTable('cursors');
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
            ->scalar('api_type')
            ->maxLength('api_type', 128)
            ->requirePresence('api_type', 'create')
            ->notEmpty('api_type');

        $validator
            ->scalar('next_cursor')
            ->maxLength('next_cursor', 128)
            ->requirePresence('next_cursor', 'create')
            ->notEmpty('next_cursor');

        return $validator;
    }
}
