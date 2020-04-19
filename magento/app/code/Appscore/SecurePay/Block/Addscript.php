<?php

namespace Appscore\SecurePay\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;


class Addscript extends \Magento\Framework\View\Element\Template
{

    
    public function __construct(Context $context)
	{
		parent::__construct($context);
	}

    public function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }
    
}