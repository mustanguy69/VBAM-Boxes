<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     ProductPricing
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\ProductPricing\PageCache;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Page unique identifier
 */
class Identifier extends \Magento\Framework\App\PageCache\Identifier
{
    /**
     * @var RequestHttp
     */
    protected $request;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Json
     */
    private $serializer;
    /**
     * @var Session
     */
    private $_customerSession;

    /**
     * @param RequestHttp $request
     * @param Context $context
     * @param Session $customerSession
     * @param Json|null $serializer
     */
    public function __construct(
        RequestHttp $request,
        Context $context,
        Session $customerSession,
        Json $serializer = null
    ) {
        parent:: __construct($request, $context, $serializer);
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->_customerSession = $customerSession;

    }

    /**
     * Return unique page identifier
     *
     * @return string
     */
    public function getValue()
    {
        $data = [
            $this->request->isSecure(),
            $this->request->getUriString(),
            $this->request->get(ResponseHttp::COOKIE_VARY_STRING)
                ?: $this->context->getVaryString(),
            $this->getCustomerGroupId()

        ];

        return sha1($this->serializer->serialize($data));
    }

    public function getCustomerGroupId()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getGroupId();
        } else {
            //-----TODO:This needs to be checked after implementing Location switcher
            return Group::NOT_LOGGED_IN_ID;
        }
    }
}
