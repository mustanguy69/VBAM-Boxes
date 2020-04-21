<?php
namespace Appscore\BranchLocator\Block;

use Appscore\BranchLocator\Model\BranchlistFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Header\Logo;
use Visy\CustomerGroup\Model\Block\Source\RegionData;

class Index extends \Magento\Framework\View\Element\Template
{

	protected $_branchlistFactory;
	
	protected $_logo;

	protected $_regionData;

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


	public function __construct(Context $context, BranchlistFactory $branchlistFactory, Logo $logo, RegionData $regionData)
	{
		$this->_branchlistFactory = $branchlistFactory;
		$this->_logo = $logo;
		$this->_regionData = $regionData;
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

	public function getBranchById($id) {
		$branch = $this->_branchlistFactory->create()->load($id);

		return $branch;
	}

	public function searchBranch($query)
	{
		$branchList = $this->_branchlistFactory->create();
		$branchList = $branchList->getCollection();
		$branchList->addFieldToSelect('*');
		$branchList->addFieldToFilter(['state', 'postcode', 'country'],
		[
			['like' => $query],
			['like' => $query],
			['like' => $query]
		]);
		
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
	
	/**
     * Get custom region id value
     *
     * @return string
     */
	public function getCustomRegionByCode($regionCode = null) {
		$region = '';

		if($regionCode) {
			$customRegionArray = $this->_regionData->getRegions();

            foreach ($customRegionArray as $key => $regionLabel) {
                if(isset($regionLabel['label']) && strtolower($regionLabel['label']) == strtolower($regionCode)) {
                    $region = $regionLabel['value'];
                    break;
                }
            }
		}

		return $region;
	}

	/**
     * Get custom region id array
     *
     * @return array
     */
	public function getRegions() {
		$customRegionArray = $this->_regionData->getRegions();
		$resultArray = [];

		foreach ($customRegionArray as $key => $regionLabel) {
			if(isset($regionLabel['label']) && trim($regionLabel['label']) != '') {
				$resultArray[$regionLabel['label']] = $regionLabel['value'];
			}
		}

		return $resultArray;
	}
}