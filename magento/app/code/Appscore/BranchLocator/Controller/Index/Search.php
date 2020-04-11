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
            $lat = $this->getRequest()->getPost('lat');
            $lng = $this->getRequest()->getPost('lng');
            $branchList = $this->_branchlistFactory->create();
            $branchList = $branchList->getCollection();
            $branchList->addFieldToSelect('*');
            $branchList->addFieldToFilter('status', array('eq' => 1));
            
            $array = [];
            foreach($branchList as $branch) {
                $branch['distance'] = $this->distance($lat, $lng, $branch->getLatitude(), $branch->getLongitude());
                $array[] = $branch->getData();
            }

            
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($array);

            return $resultJson;
        }

    }

    function distance($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;

            return ($miles * 1.609344);
        }
    }

}