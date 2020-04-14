<?php


namespace Appscore\CatalogWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Class CatalogWidget
 *
 * @package Appscore\SliderWidget\Block\Widget
 */
class CatalogWidget extends Template implements BlockInterface
{

    protected $_template = "widget/catalogwidget.phtml";

    protected $_storeManager;
    protected $categoryRepository;
    protected $imageHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public  function getSubCateList($categoryId)
    {
        $parentCategory = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        $subCategories = $parentCategory->getChildrenCategories()->addAttributeToSelect('*');

        $cateList = [];

        foreach($subCategories as $subCate) {            
            $cateObj = (object) [
                'colour' => $subCate->getData('custom_category_colour'),
                'name' => $subCate->getName(),
                'product_count' => $subCate->getProductCount(),
                'url' => $subCate->getUrl(),
                'icon' => $subCate->getData('category_icon')
            ];

            $cateList[] = $cateObj;
        }

        return $cateList;
    }
}

