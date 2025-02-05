<?php

namespace SoftDelete\ORM;

use Cake\ORM\Query\SelectQuery as SelectCakeQuery;

class SelectQuery extends SelectCakeQuery
{
    /**
     * Cake\ORM\Query::triggerBeforeFind overwritten to add the condition `deleted IS NULL` to
     * every find request to prevent returning soft deleted records.
     *
     * If the query contains the option `withDeleted` the condition `deleted IS NULL` is not applied.
     *
     * @return void
     */
    public function triggerBeforeFind(): void
    {
        if (! $this->_beforeFindFired && $this->_type === 'select') {
            parent::triggerBeforeFind();

            $options = $this->getOptions();

            if (! is_array($options) || (! in_array('withDeleted', $options, true) && ! array_key_exists('withDeleted', $options))) {
                $repository = $this->getRepository();
                $aliasedField = $repository->aliasField($repository->getSoftDeleteField());

                $this->andWhere($aliasedField . ' IS NULL');
            }
        }
    }
}
