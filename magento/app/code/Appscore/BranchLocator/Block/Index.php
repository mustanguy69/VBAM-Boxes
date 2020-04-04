<?php
namespace Appscore\BranchLocator\Block;

use Appscore\BranchLocator\Model\BranchlistFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Header\Logo;

class Index extends \Magento\Framework\View\Element\Template
{

	protected $_branchlistFactory;
	
	protected $_logo;

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


	public function __construct(Context $context, BranchlistFactory $branchlistFactory, Logo $logo)
	{
		$this->_branchlistFactory = $branchlistFactory;
		$this->_logo = $logo;
		parent::__construct($context);
	}

	public function getBranchList()
	{
		$branchList = $this->_branchlistFactory->create();
	
		return $branchList->getCollection();
	}

	public function getBranchListVic()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'VIC'));
		
		
		return $branchList;
	}

	public function getBranchListNsw()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'NSW'));
		
		
		return $branchList;
	}


	public function getBranchListQld()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'QLD'));
		
		
		return $branchList;
	}


	public function getBranchListSa()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'SA'));
		
		
		return $branchList;
	}

	public function getBranchListTas()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'TAS'));
		
		
		return $branchList;
	}

	public function getBranchListAct()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'ACT'));
		
		
		return $branchList;
	}

	public function getBranchListWa()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('state', array('eq' => 'WA'));
		
		
		return $branchList;
	}

	public function getBranchListAuckland()
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter('status', array('eq' => 1));
		$branchList->addFieldToFilter('city', array('eq' => 'Auckland'));
		
		
		return $branchList;
	}


	public function getApiKey()
	{
		$scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
		
		return $this->_scopeConfig->getValue('branchlocator/general/apiKey', $scope);
	}

	/**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {    
        return $this->_logo->getLogoSrc();
    }
}