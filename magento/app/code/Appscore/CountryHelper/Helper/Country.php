<?php


namespace Appscore\CountryHelper\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Directory\Model\CountryFactory;

/**
 * Class Country
 *
 * @package Appscore\CountryHelper\Helper
 */
class Country extends AbstractHelper
{
    protected $_countryFactory;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CountryFactory $countryFactory
    ) {
        $this->_countryFactory = $countryFactory;
        parent::__construct($context);
    }

    public function getCountryname($countryCode)
    {    
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
}

