<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\ProductPricing\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Group\RetrieverInterface as CustomerGroupRetrieverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;

/**
 * @api
 * @since 100.0.2
 */
class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice
{
    /**
     * @var CustomTierPrice
     */
    protected $_customTierPrice;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Session $customerSession
     * @param GroupManagementInterface $groupManagement
     * @param CustomTierPrice $customTierPrice
     * @param CustomerGroupRetrieverInterface|null $customerGroupRetriever
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Session $customerSession,
        GroupManagementInterface $groupManagement,
        CustomTierPrice $customTierPrice,
        CustomerGroupRetrieverInterface $customerGroupRetriever = null
    ) {
        $quantity = (float)$quantity ? $quantity : 1;
        parent::__construct(
            $saleableItem,
            $quantity,
            $calculator,
            $priceCurrency,
            $customerSession,
            $groupManagement,
            $customerGroupRetriever
        );
        $this->_customTierPrice = $customTierPrice;
    }

    /**
     * Get clear tier price list stored in DB
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStoredTierPrices()
    {
        if (null === $this->rawPriceList) {
            $this->rawPriceList = $this->product->getData(self::PRICE_CODE);

            if (null === $this->rawPriceList || !is_array($this->rawPriceList)) {
                /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
                $attribute = $this->product->getResource()->getAttribute(self::PRICE_CODE);
                if ($attribute) {
                    $attribute->getBackend()->afterLoad($this->product);
                    $this->rawPriceList = $this->product->getData(self::PRICE_CODE);
                }
            }

            //Individual Product Price List
            $this->rawPriceList = $this->_customTierPrice->getCustomerTierPrice($this->product, $this->rawPriceList);

            if (null === $this->rawPriceList || !is_array($this->rawPriceList)) {
                $this->rawPriceList = [];
            }

            if (!$this->isPercentageDiscount()) {
                foreach ($this->rawPriceList as $index => $rawPrice) {
                    if (isset($rawPrice['price'])) {
                        $this->rawPriceList[$index]['price'] =
                            $this->priceCurrency->convertAndRound($rawPrice['price']);
                    }
                    if (isset($rawPrice['website_price'])) {
                        $this->rawPriceList[$index]['website_price'] =
                            $this->priceCurrency->convertAndRound($rawPrice['website_price']);
                    }
                }
            }
        }
        return $this->rawPriceList;
    }
}
