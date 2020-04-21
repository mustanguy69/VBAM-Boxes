<?php

namespace Appscore\LatestNews\Controller\News;

use Appscore\LatestNews\Model\NewslistFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class View extends Action
{

    protected $resultPageFactory;

    protected $_newslistFactory;
    
    public function __construct(\Magento\Framework\App\Action\Context $context,
     \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     NewslistFactory $newslistFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_newslistFactory = $newslistFactory;
        parent::__construct($context);
    }
    

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $params = $this->getRequest()->getParams();
        $param = array_keys($params);
        $news = $this->_newslistFactory->create();
        $news = $news->getCollection();
		$news->addFieldToFilter('url_key', ['eq' => $param]);
        $data = $news->getFirstItem();
        $resultPage->getConfig()->getTitle()->prepend(__($data->getTitle(). " - News"));

        return $resultPage;
    }

}