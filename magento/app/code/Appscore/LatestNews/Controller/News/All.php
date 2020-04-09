<?php

namespace Appscore\LatestNews\Controller\News;

use Appscore\LatestNews\Model\NewslistFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class All extends Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    protected $_newslistFactory;
    
    /**      * @param \Magento\Framework\App\Action\Context $context      */
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
        $resultPage->getConfig()->getTitle()->prepend(__("All News"));

        return $resultPage;
    }

}