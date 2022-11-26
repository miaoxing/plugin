<?php

namespace Miaoxing\Plugin\Service;

use Wei\IsBigInt;

/**
 * @experimental
 */
class IsBigIntString extends IsBigInt
{
    /**
     * {@inheritdoc}
     */
    protected function doValidate($input)
    {
        if ('' === $input) {
            return true;
        }
        return parent::doValidate($input);
    }
}
