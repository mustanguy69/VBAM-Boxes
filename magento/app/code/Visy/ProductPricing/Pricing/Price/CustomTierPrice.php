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

use Magento\Backend\Model\Session\Quote as AdminCheckoutSession;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Visy\ProductPricing\Model\ProductPrice;
use Visy\ProductPricing\Model\ProductPriceFactory;
use Magento\Framework\Session\SessionManagerInterface;

class CustomTierPrice
{
    /**
     * @var ProductPriceFactory
     */
    protected $_productPriceFactory;
    /**
     * @var Http
     */
    protected $_request;
    /**
     * @var State
     */
    protected $_state;
    /**
     * @var QuoteFactory
     */
    protected $_quoteRepository;
    /**
     * @var SessionFactory
     */
    protected $_customerSessionFactory;
    /**
     * @var ProductPrice
     */
    protected $_productPricesCollection;
    /**
     * @var AdminCheckoutSession
     */
    protected $_adminCheckoutSession;
    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * CustomTierPrice constructor.
     * @param SessionFactory $customerSession
     * @param ProductPriceFactory $productPriceFactory
     * @param State $state
     * @param CartRepositoryInterface $quoteRepository
     * @param AdminCheckoutSession $adminCheckoutSession
     * @param Http $request
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(
        SessionFactory $customerSession,
        ProductPriceFactory $productPriceFactory,
        State $state,
        CartRepositoryInterface $quoteRepository,
        AdminCheckoutSession $adminCheckoutSession,
        Http $request,
        SessionManagerInterface $coreSession
    ) {
        $this->_request = $request;
        $this->_state = $state;
        $this->_customerSessionFactory = $customerSession;
        $this->_productPriceFactory = $productPriceFactory;
        $this->_quoteRepository = $quoteRepository;
        $this->_adminCheckoutSession = $adminCheckoutSession;
        $this->_productPricesCollection = $this->_productPriceFactory->create();
        $this->_coreSession = $coreSession;
    }

    /**
     * Get Customer Tier Price
     * @param $product
     * @param array $tirePrices
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerTierPrice($product, $tirePrices = [])
    {
        $sapCustomerCode = null;
        $cusGrp = null;
        if ($this->_state->getAreaCode() == "adminhtml") {
            //if (strpos($this->request->getUri()->getPath(), 'admin/quotes/quote/view') !== false) {
            $quoteId = $this->_request->getParam('quote_id');
            if ($quoteId == null) { //for Order creation from backend
                $quote = $this->_adminCheckoutSession->getQuote();
                $quoteId = $quote->getId();
            }

            if ($quoteId != null) {
                $quote = $this->_quoteRepository->get($quoteId);
                $customer = $quote->getCustomer();
                $sapCustomerCode = $customer->getCustomAttribute('customer_code')->getValue();
                $cusGrp = $customer->getGroupId();
            }
            //}
        } else {
            $customerSession = $this->_customerSessionFactory->create();
            $customer = $customerSession->getCustomer();
            $sapCustomerCode = $customer->getCustomerCode();
            $cusGrp = $customerSession->getCustomerGroupId();
            if (!$customerSession->isLoggedIn()) {
                $this->_coreSession->start();
                $sapCustomerCode = $this->_coreSession->getCustomerCode();
            }
        }

        $individualPrices = [];

        if ($sapCustomerCode != null && $sapCustomerCode != '') {
            $productPricesCollection = $this->_productPricesCollection->getProductCollection();
            $productSKU = $product->getSku();
            $productPricesCollection->addFilter('customer_code', $sapCustomerCode);
            $productPricesCollection->addFilter('product_sku', $productSKU);
            if (!empty($productPricesCollection->getData())) {
                $data = $productPricesCollection->getData()[0];

                $isAdd = true;
                for ($i = 1; $i <= 10; $i++) {
                    if ((int)($data['qty_' . $i]) > 0) {
                        if (!empty($tirePrices) && array_key_exists(
                            ($cusGrp . "-" . (int)($data['qty_' . $i])),
                            $tirePrices
                        )) {
                            $existPrice = $tirePrices[$cusGrp . "-" . (int)($data['qty_' . $i])];
                            if ((float)($existPrice["price"]) >= (float)($data['price_' . $i])) {
                                unset($tirePrices[$cusGrp . "-" . (int)($data['qty_' . $i])]);
                                $isAdd = true;
                            } else {
                                $isAdd = false;
                            }
                        } else {
                            $isAdd = true;
                        }
                    } else {
                        break;
                    }

                    if ($isAdd) {
                        $individualPrices[$cusGrp . "-" . (int)($data['qty_' . $i])] = [
                            "price_id" => "custom" . $i,
                            "website_id" => "all",
                            "all_groups" => "0",
                            "cust_group" => $cusGrp,
                            "price" => $data['price_' . $i],
                            "price_qty" => $data['qty_' . $i],
                            "percentage_value" => null,
                            "website_price" => $data['price_' . $i]
                        ];
                    }
                }
            }

            return array_merge($individualPrices, $tirePrices);
        }
    }
}
