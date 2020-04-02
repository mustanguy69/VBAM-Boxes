<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\ProductPricing\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Review resource model
 *
 * @api
 * @since 100.0.2
 */
class ProductPrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * ProductPrice table
     *
     * @var string
     */
    protected $_productPriceTable;

    /**
     * Review store table
     *
     * @var string
     */
    protected $_productPriceStoreTable;

    /**
     * Cache of deleted rating data
     *
     * @var array
     */
    private $_deleteCache = [];

    /**
     * Core date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->_date = $date;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $connectionName);
    }

    /**
     * Define main table. Define other tables name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('visy_customer_product_pricing', 'customer_price_id');
        $this->_productPriceTable = $this->getTable('visy_customer_product_pricing');
        $this->_productPriceStoreTable = $this->getTable('productprice_store');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        return $select;
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->_productPriceStoreTable,
            ['store_id']
        )->where(
            'customer_product_id = :customer_product_id'
        );
        $stores = $connection->fetchCol($select, [':customer_product_id' => $object->getId()]);
        if (empty($stores) && $this->_storeManager->hasSingleStore()) {
            $object->setStores([$this->_storeManager->getStore(true)->getId()]);
        } else {
            $object->setStores($stores);
        }
        return $this;
    }

    /**
     * Retrieves total ProductPrices
     *
     * @param int $entityPkValue
     * @param bool $approvedOnly
     * @param int $storeId
     * @return int
     */
    public function getTotalProductPrices($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->_productPriceTable,
            ['productprice_count' => new \Zend_Db_Expr('COUNT(*)')]
        )->where(
            "{$this->_productPriceTable}.entity_pk_value = :pk_value"
        );
        $bind = [':pk_value' => $entityPkValue];
        if ($storeId > 0) {
            $select->join(
                ['store' => $this->_productPriceStoreTable],
                $this->_productPriceTable . '.customer_product_id=store.customer_product_id AND store.store_id = :store_id',
                []
            );
            $bind[':store_id'] = (int) $storeId;
        }
        if ($approvedOnly) {
            $select->where("{$this->_productPriceTable}.status = :status");
            $bind[':status'] = \Visy\ProductPricing\Model\ProductPrice::SYNC;
        }
        return $connection->fetchOne($select, $bind);
    }

}
