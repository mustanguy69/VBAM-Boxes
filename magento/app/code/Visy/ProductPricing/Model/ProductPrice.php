<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */

namespace Visy\ProductPricing\Model;

use Magento\Framework\DataObject;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Review\Model\ResourceModel\Review\Product\Collection as ProductCollection;
use Magento\Review\Model\ResourceModel\Review\Status\Collection as StatusCollection;

/**
 * ProductPrice model
 *
 * @api
 * @method string getCreatedAt()
 * @method \Magento\Review\Model\Review setCreatedAt(string $value)
 * @method \Magento\Review\Model\Review setEntityId(int $value)
 * @method int getEntityPkValue()
 * @method \Magento\Review\Model\Review setEntityPkValue(int $value)
 * @method int getStatusId()
 * @method \Magento\Review\Model\Review setStatusId(int $value)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class ProductPrice extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'productprice';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'productprice';

    /**
     * Product entity review code
     */
    const ENTITY_PRODUCT_CODE = 'product';

    /**
     * Not Synced status
     */
    const NOT_SYNC = "NOT_SYNC";

    /**
     * Pending review status code
     */
    const SYNC = "SYNC";

    /**
     * ProductPrice product collection factory
     *
     * @var \Visy\ProductPricing\Model\ResourceModel\ProductPrice\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Url interface
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlModel;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Visy\ProductPricing\Model\ResourceModel\ProductPrice\Product\CollectionFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Visy\ProductPricing\Model\ResourceModel\ProductPrice\Product\CollectionFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlModel,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->productCollectionFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_urlModel = $urlModel;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Visy\ProductPricing\Model\ResourceModel\ProductPrice::class);
    }

    /**
     * Get product collection
     *
     * @return ProductCollection
     */
    public function getProductCollection()
    {
        return $this->productCollectionFactory->create();
    }

    /**
     * Get total ProductPrices
     *
     * @param int $entityPkValue
     * @param bool $approvedOnly
     * @param int $storeId
     * @return int
     */
    public function getTotalProductPrices($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        return $this->getResource()->getTotalProductPrices($entityPkValue, $approvedOnly, $storeId);
    }

    /**
     * Get pending status
     *
     * @return int
     */
    public function getPendingStatus()
    {
        return self::NOT_SYNC;
    }

    /**
     * Get ProductPrices product view url
     *
     * @return string
     */
    public function getProductPricesUrl()
    {
        return $this->_urlModel->getUrl('productprice/product/view', ['id' => $this->getCustomerPriceId()]);
    }

    /**
     * Get product view url
     *
     * @param string|int $productId
     * @param string|int $storeId
     * @return string
     */
    public function getProductUrl($productId, $storeId)
    {
        if ($storeId) {
            $this->_urlModel->setScope($storeId);
        }

        return $this->_urlModel->getUrl('catalog/product/view', ['id' => $productId]);
    }

    /**
     * Validate review summary fields
     *
     * @return bool|string[]
     */
    public function validate()
    {
        $errors = [];

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Perform actions after object delete
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);
        return parent::afterDeleteCommit();
    }

    /**
     * Check if current review approved or not
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatusId() == self::SYNC;
    }

    /**
     * Check if current review available on passed store
     *
     * @param int|\Magento\Store\Model\Store $store
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isAvailableOnStore($store = null)
    {
        $store = $this->_storeManager->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array)$this->getStores());
        }
        return false;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        $tags = [];
        if ($this->getEntityPkValue()) {
            $tags[] = self::CACHE_TAG . '_' . $this->getEntityPkValue();
        }
        return $tags;
    }
}
