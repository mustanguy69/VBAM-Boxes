<?php

namespace Appscore\LatestNews\Block\News;

use Appscore\LatestNews\Model\NewslistFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Header\Logo;

class View extends \Magento\Framework\View\Element\Template
{

	protected $_newslistFactory;

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


	public function __construct(Context $context, NewslistFactory $newslistFactory, Logo $logo)
	{
		$this->_newslistFactory = $newslistFactory;
		parent::__construct($context);
	}

	public function getNews()
	{
		$params = $this->getRequest()->getParams();
        $param = array_keys($params);
        $news = $this->_newslistFactory->create();
		$news = $news->getCollection();
		$news->addFieldToFilter('url_key', ['eq' => $param]);
		$data = $news->getFirstItem();
		
		return $data;
	}

	function getMediaBaseUrl($url) {
		/** @var \Magento\Framework\ObjectManagerInterface $om */
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		/** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
		/** @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $currentStore */
		$currentStore = $storeManager->getStore();
		return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $url;
	}

	function formatDateNews($date) {
		$timestamp = strtotime($date);

		$day = date('d F Y', $timestamp);

		return $day;
	}
}