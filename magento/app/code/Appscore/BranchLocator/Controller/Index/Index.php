<?php

namespace Appscore\BranchLocator\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Appscore\BranchLocator\Model\BranchlistFactory;

class Index extends Action
{
    /**
     * @var Appscore\BranchLocator\Model\BranchlistFactory;
     */
    protected $_branchlistFactory;

    /**
     * @param Context $context
     * @param BranchlistFactory $modelNewsFactory
     */
    public function __construct(
        Context $context,
        BranchlistFactory $branchlistFactory
    ) {
        parent::__construct($context);
        $this->_branchlistFactory = $branchlistFactory;
    }

    public function execute()
    {
        /**
         * When Magento get your model, it will generate a Factory class
         * for your model at var/generaton folder and we can get your
         * model by this way
         */
        $branchModel = $this->_branchlistFactory->create();

        // Get news collection
        $branchCollection = $branchModel->getCollection();
        // Load all data of collection
        var_dump($branchCollection->getData());
    }
}