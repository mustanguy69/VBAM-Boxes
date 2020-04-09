<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\ProductPricing\Plugin;

use Magento\Customer\Model\Session;
use Visy\ProductPricing\Model\ProductPriceFactory;

class Product
{
    protected $_customerSession;
    protected $prices;
    /**
     * @var ProductPriceFactory
     */
    protected $productPriceFactory;
    protected $productPricesCollection;

    /**
     * Product constructor.
     * @param Session $customerSession
     */
    public function __construct(
        Session $customerSession,
        ProductPriceFactory $productPriceFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->productPriceFactory = $productPriceFactory;
    }

    /**
     * After GetPrice
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return float|int
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($this->_customerSession->isLoggedIn()) {
            //$this->_customerSession->getCustomer()->getId();
            if (!$this->productPricesCollection) {
                $this->productPricesCollection = $this->productPriceFactory->create()->getProductCollection();
            }

            $customerData = $this->_customerSession->getCustomer();
            $sapCustomerCode = $customerData->getCustomerCode();
            if ($sapCustomerCode != null && $sapCustomerCode != '') {
                $productSKU = $subject->getSku();
                $this->productPricesCollection->addFilter('customer_code', $sapCustomerCode);
                $this->productPricesCollection->addFilter('product_sku', $productSKU);

                $customPrice = 1;
                $result = $result * $customPrice;
            }
        }

        return $result;
    }
}
