<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GroupMapping extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('visy_customergroup_map', 'visy_customergroup_map_id');
    }
}
