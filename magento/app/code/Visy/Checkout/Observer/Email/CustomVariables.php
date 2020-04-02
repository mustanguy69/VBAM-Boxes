<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\Checkout\Observer\Email;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class CustomVariables implements ObserverInterface
{
    protected $helper;
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getTransport();
        $order = $transport['order'];
        $transport['formattedDeliveryDate'] = $this->getDeliveryDateFormatted($order);
    }

    /**
     * Get formatted order delivery date
     *
     * @param Order $order
     * @param bool $showTime
     * @return string
     * @throws \Exception
     */
    public function getDeliveryDateFormatted(Order $order, $showTime = false)
    {
        if (!$order->getDeliveryDate()) {
            return;
        }
        /*$timezone = $this->timezone->getConfigTimezone(
            ScopeInterface::SCOPE_STORE,
            0
        );*/
        $date = new \DateTime($order->getDeliveryDate());
        //return $date->format("j M Y");
        $formattedDate = $this->timezone->formatDateTime(
            $date,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM,
            null,
            null,
            "d/MM/Y"
        );

        return $formattedDate;
    }
}
