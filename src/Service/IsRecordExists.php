<?php

namespace Miaoxing\Plugin\Service;

use Wei\Record;
use Wei\Validator\RecordExists;

class IsRecordExists extends RecordExists
{
    /**
     * {@inheritdoc}
     */
    protected function doValidate($input)
    {
        if (!$this->table instanceof Record) {
            return parent::doValidate($input);
        }

        if ($this->field) {
            $this->table->andWhere([$this->field => $input]);
        }

        if (!$this->table->fetchColumn()) {
            $this->addError('notFound');
            return false;
        } else {
            return true;
        }
    }
}
