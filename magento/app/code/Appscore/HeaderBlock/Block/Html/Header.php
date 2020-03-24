<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Appscore\HeaderBlock\Block\Html;

/**
 * Html page header block
 *
 * @api
 * @since 100.0.2
 */
class Header extends \Magento\Theme\Block\Html\Header
{
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        array $data = []
    ) {
        $this->_customerUrl = $customerUrl;
        $this->_wishlistHelper = $wishlistHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve welcome text
     *
     * @return string
     */
    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            $this->_data['welcome'] = $this->_scopeConfig->getValue(
                'design/header/welcome',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return __($this->_data['welcome']);
    }

    public function getCustomerUrl()
    {
        return $this->_customerUrl->getAccountUrl();
    }

    public function getWishlistUrl()
    {
        if ($this->_wishlistHelper->isAllow()) {
            return $this->getUrl('wishlist');
        }
    }
}
