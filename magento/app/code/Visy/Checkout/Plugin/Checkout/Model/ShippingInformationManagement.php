<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\Checkout\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\QuoteRepository;

class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Session $customerSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_customerSession = $customerSession;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $deliveryDate = $extAttributes->getDeliveryDate();
        $customerCode = null;
        if ($this->_customerSession->isLoggedIn()) {
            $customer = $this->_customerSession->getCustomer();
            if ($customer->getCustomerCode() != null && $customer->getCustomerCode() != '') {
                $customerCode = $customer->getCustomerCode();
            }
        }
        $extAttributes->setCustomerCode($customerCode);
        $addressInformation->setExtensionAttributes($extAttributes);
        //$deliveryComment = $extAttributes->getDeliveryComment();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setDeliveryDate($deliveryDate);
        $quote->setCustomerCode($customerCode);
        //$quote->setDeliveryComment($deliveryComment);
    }
}
