<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\ProductPricing\Model\ResourceModel\ProductPrice\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitationFactory;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * ProductPrice Product Collection
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @since 100.0.2
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Add store data flag
     *
     * @var bool
     */
    protected $_addStoreDataFlag = false;

    /**
     * Filter by stores for the collection
     *
     * @var array
     */
    protected $_storesIds = [];

    protected $_productRepository;

    /**
     * Collection constructor
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param ProductLimitationFactory|null $productLimitationFactory
     * @param MetadataPool|null $metadataPool
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ProductLimitationFactory $productLimitationFactory = null,
        MetadataPool $metadataPool = null
    ) {
        $this->_productRepository = $productRepository;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection,
            $productLimitationFactory,
            $metadataPool
        );
    }

    /**
     * Define module
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Catalog\Model\Product::class, \Magento\Catalog\Model\ResourceModel\Product::class);
        $this->setRowIdFieldName('customer_price_id');
        //$this->_productPriceStoreTable = $this->_resource->getTableName('productprice_store');
        $this->_initTables();
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Adds store filter into array
     *
     * @param int|int[] $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStoreId();
        }

        parent::addStoreFilter($storeId);

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }

    /**
     * Adds specific store id into array
     *
     * @param array $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        if (is_array($storeId) && isset($storeId['eq'])) {
            $storeId = array_shift($storeId);
        }

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }

    /**
     * Applies all store filters in one place to prevent multiple joins in select
     *
     * @param Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(Select $select = null)
    {
        $connection = $this->getConnection();
        $storesIds = $this->_storesIds;
        if (null === $select) {
            $select = $this->getSelect();
        }

        if (is_array($storesIds) && count($storesIds) == 1) {
            $storesIds = array_shift($storesIds);
        }

        /*if (is_array($storesIds) && !empty($storesIds)) {
            $inCond = $connection->prepareSqlCondition('store.store_id', ['in' => $storesIds]);
            $select->join(
                ['store' => $this->_productPriceStoreTable],
                'rt.customer_price_id=store.customer_price_id AND ' . $inCond,
                []
            )->group(
                'rt.customer_price_id'
            );
        } else {
            $select->join(
                ['store' => $this->_productPriceStoreTable],
                $connection->quoteInto('rt.customer_price_id=store.customer_price_id AND store.store_id = ?', (int)$storesIds),
                []
            );
        }*/

        return $this;
    }

    /**
     * Add stores data
     *
     * @return $this
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Add entity filter
     *
     * @param int $entityId
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addEntityFilter($entityId)
    {
        if ($entityId) {
            $sku = $this->getProductById($entityId)->getSku();
            $this->getSelect()->where('rt.product_sku = ?', $sku);
        }

        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return $this
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('rt.created_at', $dir);
        return $this;
    }

    /**
     * Join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $productpriceTable = $this->_resource->getTableName('visy_customer_product_pricing');

        $this->addAttributeToSelect('name')->addAttributeToSelect('sku');

        $this->getSelect()->join(
            ['rt' => $productpriceTable],
            'rt.product_sku = e.sku'//,
            //['rt.customer_price_id', 'productprice_created_at' => 'rt.created_at', 'rt.product_entity_id', 'rt.status']
        );
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @param null|int|string $limit
     * @param null|int|string $offset
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->columns('rt.customer_price_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Get result sorted ids
     *
     * @return array
     */
    public function getResultingIds()
    {
        $idsSelect = clone $this->getSelect();
        $data = $this->getConnection()
            ->fetchAll(
                $idsSelect
                    ->reset(Select::LIMIT_COUNT)
                    ->reset(Select::LIMIT_OFFSET)
                    ->columns('rt.customer_price_id')
            );

        return array_map(
            function ($value) {
                return $value['customer_price_id'];
            },
            $data
        );
    }

    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->reset(Select::COLUMNS)->columns('COUNT(e.entity_id)')->reset(Select::HAVING);

        return $select;
    }

    /**
     * Set order to attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = 'DESC')
    {
        switch ($attribute) {
            case 'rt.customer_price_id':
            case 'rt.created_at':
            case 'rt.updated_at':
            case 'rt.product_sku':
            case 'rt.qty_1':
            case 'rt.qty_2':
            case 'rt.qty_3':
            case 'rt.qty_4':
            case 'rt.qty_5':
            case 'rt.qty_6':
            case 'rt.qty_7':
            case 'rt.qty_8':
            case 'rt.qty_9':
            case 'rt.qty_10':
            case 'rt.price_1':
            case 'rt.price_2':
            case 'rt.price_3':
            case 'rt.price_4':
            case 'rt.price_5':
            case 'rt.price_6':
            case 'rt.price_7':
            case 'rt.price_8':
            case 'rt.price_9':
            case 'rt.price_10':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            case 'stores':
                // No way to sort
                break;
            default:
                parent::setOrder($attribute, $dir);
                break;
        }
        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param AbstractAttribute|string $attribute
     * @param array|null $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch ($attribute) {
            case 'rt.customer_price_id':
            case 'rt.created_at':
            case 'rt.updated_at':
            case 'rt.customer_code':
            case 'rt.product_sku':
            case 'rt.price_1':
            case 'rt.price_2':
            case 'rt.price_3':
            case 'rt.price_4':
            case 'rt.price_5':
            case 'rt.price_6':
            case 'rt.price_7':
            case 'rt.price_8':
            case 'rt.price_9':
            case 'rt.price_10':
            case 'rt.qty_1':
            case 'rt.qty_2':
            case 'rt.qty_3':
            case 'rt.qty_4':
            case 'rt.qty_5':
            case 'rt.qty_6':
            case 'rt.qty_7':
            case 'rt.qty_8':
            case 'rt.qty_9':
            case 'rt.qty_10':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;
            case 'stores':
                $this->setStoreFilter($condition);
                break;
            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }

    /**
     * Retrieves column values
     *
     * @param string $colName
     * @return array
     */
    public function getColumnValues($colName)
    {
        $col = [];
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }

    /**
     * Action after load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /*if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }*/
        return $this;
    }

    /**
     * Not add store ids to items
     *
     * @return $this
     * @since 100.2.8
     */
    protected function prepareStoreId()
    {
        return $this;
    }

    /**
     * Add store data
     *
     * @return void
     */
    protected function _addStoreData()
    {
        $connection = $this->getConnection();
        $productPricesIds = $this->getColumnValues('customer_price_id');
        $storesToProductPrices = [];
        /*if (count($productPricesIds) > 0) {
            $productPriceIdCondition = $this->_getConditionSql('customer_price_id', ['in' => $productPricesIds]);
            $storeIdCondition = $this->_getConditionSql('store_id', ['gt' => 0]);
            $select = $connection->select()->from(
                $this->_productPriceStoreTable
            )->where(
                $productPriceIdCondition
            )->where(
                $storeIdCondition
            );
            $result = $connection->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToProductPrices[$row['customer_price_id']])) {
                    $storesToProductPrices[$row['customer_price_id']] = [];
                }
                $storesToProductPrices[$row['customer_price_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToProductPrices[$item->getCustomerPriceId()])) {
                $item->setData('stores', $storesToProductPrices[$item->getCustomerPriceId()]);
            } else {
                $item->setData('stores', []);
            }
        }*/
    }

    /**
     * Redeclare parent method for store filters applying
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        //$this->_applyStoresFilterToSelect();

        return $this;
    }

    /**
     * Get Product By Id
     * @param $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
}
