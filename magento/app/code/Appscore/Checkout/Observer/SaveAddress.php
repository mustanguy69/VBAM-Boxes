<?php 

namespace Appscore\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface; 

class SaveAddress implements ObserverInterface { 

    protected $quoteFactory;
    
    protected $branchlistFactory;

    public function __construct(\Magento\Quote\Model\QuoteFactory $quoteFactory,
     \Appscore\BranchLocator\Model\BranchlistFactory $branchlistFactory,
     \Magento\Directory\Model\RegionFactory $regionFactory) {
        $this->quoteFactory = $quoteFactory;
        $this->branchlistFactory = $branchlistFactory;
        $this->_regionFactory = $regionFactory;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) { 
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $quote = $this->quoteFactory->create()->load($order->getQuoteId());
        
        
        if($quote->getClickAndCollectId() !== null) {
            $store = $this->branchlistFactory->create()->load($quote->getClickAndCollectId());
            $region = $this->_regionFactory->create();
            $region = $region->loadByCode($store->getState(), $store->getCountry());
            $regionId = $region->getId();
            $regionName = $region->getName();

            $quote->getShippingAddress()->setFirstname($store->getName());
            $quote->getShippingAddress()->setLastname('- Click and Collect');
            $quote->getShippingAddress()->setStreet($store->getAddress());
            $quote->getShippingAddress()->setPostcode($store->getPostcode());
            $quote->getShippingAddress()->setCity($store->getCity());
            $quote->getShippingAddress()->setTelephone($store->getPhone());
            $quote->getShippingAddress()->setFax($store->getFax());
            $quote->getShippingAddress()->setRegionId($regionId);
            $quote->getShippingAddress()->setRegion($regionName);
            $quote->getShippingAddress()->setCountryId($store->getCountry());

            $quote->save();

            $order->getShippingAddress()->setFirstname($store->getName());
            $order->getShippingAddress()->setLastname(' - Click and Collect');
            $order->getShippingAddress()->setStreet($store->getAddress());
            $order->getShippingAddress()->setPostcode($store->getPostcode());
            $order->getShippingAddress()->setCity($store->getCity());
            $order->getShippingAddress()->setTelephone($store->getPhone());
            $order->getShippingAddress()->setFax($store->getFax());
            $order->getShippingAddress()->setRegionId($regionId);
            $order->getShippingAddress()->setRegion($regionName);
            $order->getShippingAddress()->setCountryId($store->getCountry());

            $order->save();
        }
    
    }
}