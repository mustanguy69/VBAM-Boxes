<?php

namespace Appscore\BranchLocator\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class ApiKey extends Action
{
    protected $scopeConfig;
    
    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(\Magento\Framework\App\Action\Context $context,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    

    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
    
            $configValue = $this->scopeConfig->getValue(
                'branchlocator/general/apiKey',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($configValue);
            

            return $resultJson;
        }

    }

}