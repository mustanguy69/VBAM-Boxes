<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Appscore\CatalogWidget\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;

/**
 * Product list
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSession;

    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    ) {
        $this->_wishlist = $wishlist;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    public function isInWishlist($id) {
        $customerSession = $this->_customerSession->create();
        $flag = false;

        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $wishlist = $this->_wishlist->loadByCustomerId($customer->getId())->getItemCollection();
            
            foreach($wishlist as $item) {
                if($item->getProduct()->getId() == $id) {
                    $flag = true;
                    break;
                }
            }
        }

        return $flag;
    }
}
