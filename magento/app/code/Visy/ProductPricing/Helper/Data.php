<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\ProductPricing\Helper;

/**
 * Default review helper
 *
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter
    ) {
        $this->_escaper = $escaper;
        $this->filter = $filter;
        parent::__construct($context);
    }
    /**
     * Get ProductPrice statuses with their codes
     *
     * @return array
     */
    public function getProductPriceStatuses()
    {
        return [
            \Visy\ProductPricing\Model\ProductPrice::NOT_SYNC => __('Not Synced'),
            \Visy\ProductPricing\Model\ProductPrice::SYNC => __('Synced')
        ];
    }

    /**
     * Get ProductPrice statuses as option array
     *
     * @return array
     */
    public function getProductPriceStatusesOptionArray()
    {
        $result = [];
        foreach ($this->getProductPriceStatuses() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label->getText()];
        }

        return $result;
    }

}
