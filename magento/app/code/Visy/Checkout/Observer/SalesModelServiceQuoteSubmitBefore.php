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

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Visy\Checkout\Model\Validator;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * SalesModelServiceQuoteSubmitBefore constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param Validator $validator
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Validator $validator
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->validator = $validator;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($order->getQuoteId());
        if (!$this->validator->validate($quote->getDeliveryDate())) {
            throw new \Exception(__('Invalid Delivery Date'));
        }
        $order->setDeliveryDate($quote->getDeliveryDate());
        $order->setCustomerCode($quote->getCustomerCode());
        //$order->setDeliveryComment($quote->getDeliveryComment());

        return $this;
    }
}