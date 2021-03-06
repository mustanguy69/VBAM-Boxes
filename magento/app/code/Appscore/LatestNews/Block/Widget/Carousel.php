<?php

namespace Appscore\LatestNews\Block\Widget;

use Appscore\LatestNews\Model\NewslistFactory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface; 

class Carousel extends Template implements BlockInterface
{

	protected $_newslistFactory;

	protected $_template = "widget/carousel.phtml";

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,  
		\Magento\Framework\ObjectManagerInterface $objectManager,
		array $data = [],
		NewslistFactory $newslistFactory
	 ) {
		parent::__construct($context, $data);
		$this->customerSession = $customerSession;
		$this->_objectManager = $objectManager;
		$this->_newslistFactory = $newslistFactory;
	}

	public function getNewsList($ids, $currentId = null)
	{
        $newsList = $this->_newslistFactory->create();
        $newsList = $newsList->getCollection();
		$newsList->addFieldToSelect('*');
		$newsList->addFieldToFilter('status', ['eq' => '1']);
		$idsExploded = explode(',', $ids);

		foreach ($idsExploded as $id) {
			$idToFetch[] = ['eq' => [$id]];
		}

		$newsList->addFieldToFilter(['category_id', 'category_id'],
		[
			$idToFetch,
			['eq' => [$ids]]
		]);

		$newsList->setOrder('created_at', 'DESC');
		if($currentId !== null) {
			$newsList->addFieldToFilter('id', ['neq' => $currentId]);
			$newsList->setPageSize(2);
		} else  {
			$newsList->setPageSize(3);
		}
	
		return $newsList;
	}

	function getMediaBaseUrl($url) {
		
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
		$currentStore = $storeManager->getStore();

		return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $url;
	}

	function formatDateNews($date) {
		$timestamp = strtotime($date);
		$day = date('d F Y', $timestamp);

		return $day;
	}
}