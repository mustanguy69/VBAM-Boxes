<?php

namespace Appscore\PartnerPortal\Block;

class AddCssVar extends \Magento\Framework\View\Element\Template
{
    
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
	)
	{
		parent::__construct($context);
    }
    

	public function getConfig($color){

        return $this->_scopeConfig->getValue('design/colors/'.$color, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}