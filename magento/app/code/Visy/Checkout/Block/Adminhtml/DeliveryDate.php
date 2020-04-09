<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\Checkout\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Visy\Checkout\Model\Config;

class DeliveryDate extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * DeliveryDate constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Json $json,
        array $data = []
    ) {
        $this->config = $config;
        $this->json = $json;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->json->serialize($this->config->getConfig());
    }

    /**
     * @return mixed
     */
    public function getRequiredDeliveryDate()
    {
        return $this->config->getRequiredDeliveryDate();
    }

    /**
     * @return mixed
     */
    public function getDisableDeliveryDate()
    {
        return $this->config->getDisableDeliveryDate();
    }

    public function isDeliveryDateSet($deliveryDate)
    {
        if ($deliveryDate == null) {
            return false;
        } else {
            return true;
        }
    }
}
