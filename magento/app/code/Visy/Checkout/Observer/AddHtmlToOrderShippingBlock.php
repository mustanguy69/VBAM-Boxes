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
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\TemplateFactory;
use Magento\Store\Model\ScopeInterface;

class AddHtmlToOrderShippingBlock implements ObserverInterface
{
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * AddHtmlToOrderShippingBlock constructor.
     *
     * @param TemplateFactory $templateFactory
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        TemplateFactory $templateFactory,
        TimezoneInterface $timezone
    ) {
        $this->templateFactory = $templateFactory;
        $this->timezone = $timezone;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'sales.order.info') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            if ($order->getDeliveryDate() != null) {
                $formattedDate = $this->formatDate(
                    $order->getDeliveryDate(),
                    $order->getStore()->getCode(),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE
                );
            } else {
                $formattedDate = __('N/A');
            }

            if ($order->getSapSentDate() != null) {
                $formattedSapDate = $this->formatDate(
                    $order->getSapSentDate(),
                    $order->getStore()->getCode(),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::MEDIUM
                );
            } else {
                $formattedSapDate = __('N/A');
            }

            $customerCode = $order->getCustomerCode();
            $sapSentStatus = $order->getSapSentStatus();

            /** @var \Magento\Framework\View\Element\Template $deliveryDateBlock */
            $deliveryDateBlock = $this->templateFactory->create();
            $deliveryDateBlock->setDeliveryDate($formattedDate);
            $deliveryDateBlock->setCustomerCode($customerCode);
            $deliveryDateBlock->setSapSentStatus($sapSentStatus);
            $deliveryDateBlock->setSapSentDate($formattedSapDate);
            $deliveryDateBlock->setDeliveryComment($order->getDeliveryComment());
            $deliveryDateBlock->setTemplate('Visy_Checkout::order_info_shipping_info.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }

        return $this;
    }

    /**
     * Format Date
     * @param $dateString
     * @param int $scopeCode
     * @param int $dateFormat
     * @param int $timeFormat
     * @return string
     */
    public function formatDate(
        $dateString,
        $scopeCode = 1,
        $dateFormat = \IntlDateFormatter::MEDIUM,
        $timeFormat = \IntlDateFormatter::MEDIUM
    ) {
        return $this->timezone->formatDateTime(
            $dateString,
            $dateFormat,
            $timeFormat,
            null,
            $this->timezone->getConfigTimezone(
                ScopeInterface::SCOPE_STORE,
                $scopeCode
            )
        );
    }
}
