<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\ProductPricing\Ui\Component\Listing\Columns;

use Magento\Framework\Pricing\Helper\Data as PricingData;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * @api
 * @since 101.0.0
 */
class PriceText extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'price_';
    protected $_pricingData;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PricingData $pricingData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PricingData $pricingData,
        array $components = [],
        array $data = []
    ) {
        $this->_pricingData = $pricingData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @since 101.0.0
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            for ($i = 1; $i <= 10; $i++) {
                if (!empty($item[static::NAME . $i]) && $item[static::NAME . $i]>0) {
                    $item[static::NAME . $i] = $this->_pricingData->currency($item[static::NAME . $i], true, false);
                } else {
                    $item[static::NAME . $i] = "-";
                }
            }
        }

        return $dataSource;
    }
}
