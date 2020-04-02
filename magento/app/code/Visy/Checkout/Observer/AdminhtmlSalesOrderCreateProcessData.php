<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\Checkout\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AdminhtmlSalesOrderCreateProcessData implements ObserverInterface
{
    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    public function __construct(
        CustomerRepository $customerRepository
    ) {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $requestData = $observer->getRequest();
        //$email = $requestData['order']['account']['email'];
        //$this->_customerRepository->get($email);
        /** @var \Magento\Sales\Model\AdminOrder\Create $orderCreateModel */
        $orderCreateModel = $observer->getOrderCreateModel();
        $quote = $orderCreateModel->getQuote();
        if (isset($requestData['customer_id'])) {
            $customer = $this->_customerRepository->getById($requestData['customer_id']);
            $customerCode = $customer->getCustomAttribute('customer_code')->getValue();
            $quote->setCustomerCode($customerCode);
        }
        $deliveryDate = isset($requestData['delivery_date']) ? $requestData['delivery_date'] : null;
        //$deliveryComment = isset($requestData['delivery_comment']) ? $requestData['delivery_comment'] : null;

        $quote->setDeliveryDate($deliveryDate);
        //$quote->setDeliveryComment($deliveryComment);

        return $this;
    }
}
