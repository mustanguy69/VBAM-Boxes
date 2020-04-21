<?php

namespace Appscore\BranchLocator\Controller\Index;

use Appscore\BranchLocator\Model\BranchlistFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    protected $_branchlistFactory;
    
    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    BranchListFactory $branchlistFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_branchlistFactory = $branchlistFactory;
        parent::__construct($context);
    }
    

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Branch Locator'));

        if ($this->getRequest()->isAjax()) {
            $id = $this->getRequest()->getPost('id');
            $branchlist = $this->_branchlistFactory->create()->load($id);

            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($branchlist);
            
            return $resultJson;
        }

        return $resultPage;
    }

}