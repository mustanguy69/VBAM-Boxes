<?php

namespace Appscore\LatestNews\Block\News;

use Appscore\LatestNews\Model\Newslist;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Header\Logo;

class All extends \Magento\Framework\View\Element\Template
{

    protected $_newslistCollection;
    
    public function __construct(Context $context, Newslist $newslistCollection, Logo $logo)
	{
		$this->_newslistCollection = $newslistCollection;
		parent::__construct($context);
	}

    public function _prepareLayout()
    {
        parent::_prepareLayout();

        
        if ($this->getAllNews()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'custom.news.pager'
            )->setLimit(9)->setAvailableLimit([3 => 3, 9 => 9])->setShowPerPage(true)->setCollection(
                $this->getAllNews()
            );
            $this->setChild('pager', $pager);
            $this->getAllNews()->load();
        }

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	

	public function getAllNews()
	{
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 9;
        $newsCollection = $this->_newslistCollection->getCollection();
        $newsCollection->addFieldToFilter('status', '1');
        $newsCollection->setOrder('created_at','DESC'); 
        $newsCollection->setPageSize($pageSize);
        $newsCollection->setCurPage($page);
        
		return $newsCollection;
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