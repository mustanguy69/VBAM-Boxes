<?php
namespace Visy\RestApi\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

interface VisyIntegrationInterface {
    /**
	 * Constructor
	 * @return null
	 */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList
    );

    /**
	 * Upload .csv customer product/pricing.
	 * @param file $file
	 * @return null
	 */
    public function postProductPricing();
}
