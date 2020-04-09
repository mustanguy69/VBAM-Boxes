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

use Magento\Framework\Event\ObserverInterface;

class SalesOrderLoadAfter implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }
        $customerCode = $order->getData('customer_code');
        $extensionAttributes->setCustomerCode($customerCode);
        $deliveryDate = $order->getData('delivery_date');
        if ($deliveryDate != null) {
            $extensionAttributes->setDeliveryDate($deliveryDate);
        } else {
            $extensionAttributes->setDeliveryDate('N/A');
        }
        $order->setExtensionAttributes($extensionAttributes);
    }

    private function getOrderExtensionDependency()
    {
        $orderExtension = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Sales\Api\Data\OrderExtension'
        );
        return $orderExtension;
    }
}
