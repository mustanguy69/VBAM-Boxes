<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Customer
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\CustomerGroup\Model\Block\Source;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerGroupData
 */
class CustomerGroupData implements OptionSourceInterface
{
    /**
     *
     * @var Collection
     */
    protected $_customerGroupCollection;

    /**
     * Constructor
     *
     * @param Collection $customerGroupCollection
     * @param array $data
     */
    public function __construct(
        Collection $customerGroupCollection,
        array $data = []
    ) {
        $this->_customerGroupCollection = $customerGroupCollection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $customerGroups = $this->_customerGroupCollection;
        $options = [];
        foreach ($customerGroups as $key => $value) {
            $options[] = [
                'label' => $value->getCustomerGroupCode(),
                'value' => $key,
            ];
        }
        return $options;
    }
}
