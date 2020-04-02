<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\ProductPricing\Model\ResourceModel\ProductPrice;

/**
 * Review collection resource model
 *
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ProductPrice table
     *
     * @var string
     */
    protected $_productPriceTable = null;

    /**
     * ProductPrice store table
     *
     * @var string
     */
    protected $_productPriceStoreTable = null;

    /**
     * Add store data flag
     * @var bool
     */
    protected $_addStoreDataFlag = false;

    /**
     * Review data
     *
     * @var \Visy\ProductPricing\Helper\Data
     */
    protected $_productPriceData = null;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Visy\ProductPricing\Helper\Data $productPriceData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Visy\ProductPricing\Helper\Data $reviewData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_productPriceData = $reviewData;
        $this->_storeManager = $storeManager;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define module
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Visy\ProductPricing\Model\ProductPrice::class, \Visy\ProductPricing\Model\ResourceModel\ProductPrice::class);
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int|int[] $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $inCond = $this->getConnection()->prepareSqlCondition('store.store_id', ['in' => $storeId]);
        $this->getSelect()->join(
            ['store' => $this->getReviewStoreTable()],
            'main_table.customer_price_id=store.customer_price_id',
            []
        );
        $this->getSelect()->where($inCond);
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
     * Add status filter
     *
     * @param int|string $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        if (is_string($status)) {
            $statuses = array_flip($this->_productPriceData->getProductPriceStatuses());
            $status = isset($statuses[$status]) ? $statuses[$status] : 0;
        }

        $this->addFilter('status', $this->getConnection()->quoteInto('main_table.status=?', $status), 'string');

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
        $this->setOrder('main_table.created_at', $dir);
        return $this;
    }

    /**
     * Load data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_eventManager->dispatch('productprice_productprice_collection_load_before', ['collection' => $this]);
        parent::load($printQuery, $logQuery);
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
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
        if (count($productPricesIds) > 0) {
            $inCond = $connection->prepareSqlCondition('customer_price_id', ['in' => $productPricesIds]);
            $select = $connection->select()->from($this->getProductPriceStoreTable())->where($inCond);
            $result = $connection->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToProductPrices[$row['customer_price_id']])) {
                    $storesToProductPrices[$row['customer_price_id']] = [];
                }
                $storesToProductPrices[$row['customer_price_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToProductPrices[$item->getId()])) {
                $item->setStores($storesToProductPrices[$item->getId()]);
            } else {
                $item->setStores([]);
            }
        }
    }

    /**
     * Get ProductPrice table
     *
     * @return string
     */
    protected function getProductPriceTable()
    {
        if ($this->_productPriceTable === null) {
            $this->_productPriceTable = $this->getTable('visy_customer_product_pricing');
        }
        return $this->_productPriceTable;
    }

    /**
     * Get ProductPrice store table
     *
     * @return string
     */
    protected function getProductPriceStoreTable()
    {
        if ($this->_productPriceStoreTable === null) {
            $this->_productPriceStoreTable = $this->getTable('productprice_store');
        }
        return $this->_productPriceStoreTable;
    }
}
