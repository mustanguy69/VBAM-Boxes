<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class GroupMapping extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'visy_customergroup_map';

    protected function _construct()
    {
        $this->_init('Visy\CustomerGroup\Model\ResourceModel\GroupMapping');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }


}
