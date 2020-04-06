<?php

namespace Appscore\BranchLocator\Controller\Index;

use Appscore\BranchLocator\Model\BranchlistFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Search extends Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    protected $_branchlistFactory;
    
    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(\Magento\Framework\App\Action\Context $context,
     \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     BranchListFactory $branchlistFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_branchlistFactory = $branchlistFactory;
        parent::__construct($context);
    }
    

    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $query = $this->getRequest()->getPost('query');
            $branchList = $this->_branchlistFactory->create();
            $branchList = $branchList->getCollection();
            $branchList->addFieldToSelect('*');
            $branchList->addFieldToFilter('status', array('eq' => 1));
            $branchList->addFieldToFilter(
                array('postcode','city', 'address'),
                array(
                    array('like' => '%'.$query.'%'),
                    array('like' => '%'.$query.'%'),
                    array('like' => '%'.$query.'%')
                )
                            
            );
            
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($branchList->getData());
            

            return $resultJson;
        }

    }

}