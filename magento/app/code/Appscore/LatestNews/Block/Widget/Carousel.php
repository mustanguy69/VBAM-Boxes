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

	public function getNewsList()
	{
        $newsList = $this->_newslistFactory->create();
        $newsList = $newsList->getCollection();
        $newsList->addFieldToSelect('*');
        $newsList->setOrder('updated_at', 'ASC');
        $newsList->setPageSize(3);
	
		return $newsList;
	}
}