<?php

namespace Appscore\CatalogFilter\Block;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Category;

/**
 * Class CatalogFilter
 *
 * @package Appscore\CatalogFilter\Block
 */
class CatalogFilter extends \Magento\Framework\View\Element\Template
{
    protected $_storeManager;
    protected $_cateCollectionFactory;
    protected $_cateHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Category $cateHelper,
        array $data = []
    ) {
        $this->_cateCollectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->_cateHelper = $cateHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return collection
     */
    public function getParentCategories()
    {
        $categories = $this->_cateCollectionFactory->create()->addAttributeToSelect('*')->addIsActiveFilter()->addAttributeToFilter('level','2')->setStore($this->_storeManager->getStore());

        return $categories;
    }

    /**
     * @return collection
     */
    public function getChildCategories($category)
    {
        return $category->getChildrenCategories();
    }
}

