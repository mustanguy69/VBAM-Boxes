<?php


namespace Appscore\SliderWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Class CategorySlider
 *
 * @package Appscore\SliderWidget\Block\Widget
 */
class CategorySlider extends Template implements BlockInterface
{

    protected $_template = "widget/categoryslider.phtml";

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
        $subCategories = $parentCategory->getChildrenCategories();

        $cateList = [];

        foreach($subCategories as $subCate) {
            $firstProductInStock = $subCate->getProductCollection()->addAttributeToSelect('*')->setPageSize(1)->getFirstItem();

            if($firstProductInStock) {
                $image_url = $this->imageHelper->init($firstProductInStock, 'custom_product_base_image')->getUrl();
            }

            $cateObj = (object) [
                'color' => '',
                'name' => $subCate->getName(),
                'product_count' => $subCate->getProductCount(),
                'url' => $subCate->getUrl(),
                'first_product' => (object) [
                    'name' => $firstProductInStock->getName(),
                    'image' => $image_url ? $image_url : '',
                    'url' => $firstProductInStock->getProductUrl(),
                    'price' => number_format($firstProductInStock->getPrice(), 2, '.', ','), // TODO: setup logic to display price based on customer group or guest user location
                ]
            ];

            $cateList[] = $cateObj;
        }

        return $cateList;
    }
}

