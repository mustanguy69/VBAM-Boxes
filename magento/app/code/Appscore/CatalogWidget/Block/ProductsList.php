<?php
namespace Appscore\CatalogWidget\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutFactory;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Url\EncoderInterface;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
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
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     * @param Json|null $json
     * @param LayoutFactory|null $layoutFactory
     * @param \Magento\Framework\Url\EncoderInterface|null $urlEncoder
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Wishlist\Model\Wishlist $wishlist, // add my custom helper in here before array. remember to run di:compile to clear out old interceptor
        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null
    ) {
        $this->_wishlist = $wishlist;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
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