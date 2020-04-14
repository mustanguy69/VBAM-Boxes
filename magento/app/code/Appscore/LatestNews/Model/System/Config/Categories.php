<?php

namespace Appscore\LatestNews\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;
use Appscore\LatestNews\Model\CategoriesFactory;

class Categories implements ArrayInterface
{
    protected $_categoriesFactory;

    public function __construct(CategoriesFactory $categoriesFactory)
    {
        $this->_categoriesFactory = $categoriesFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_categoriesFactory->create()->getCollection();
        $options = [];
        foreach ($collection as $value) {
            $options[] = ['value' => $value->getId(), 'label' => $value->getName()];
        }

        return $options;
    }
}