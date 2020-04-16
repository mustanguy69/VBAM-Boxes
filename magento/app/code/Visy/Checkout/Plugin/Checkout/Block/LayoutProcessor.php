<?php
/**
 * VISY IDT Ltd.
 *
 * @category    Visy
 * @package     Checkout
 * @author      VISY IDT Team <contact@visy.com>
 * @copyright   Copyright (c) 2020 Visy IDT Ltd. (http://visy.com.au)
 */
namespace Visy\Checkout\Plugin\Checkout\Block;

use Visy\Checkout\Model\Config;

class LayoutProcessor
{
    /**
     * @var \Visy\Checkout\Model\Config
     */
    protected $config;

    /**
     * LayoutProcessor constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        $disableDeliveryDate =  $this->config->getDisableDeliveryDate() ?: false;
        if (!$disableDeliveryDate) {
            $requiredDeliveryDate =  $this->config->getRequiredDeliveryDate() ?: false;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shippingAdditional'] = [
                'component' => 'uiComponent',
                'displayArea' => 'shippingAdditional',
                'children' => [
                    'delivery_date' => [
                        'component' => 'Visy_Checkout/js/view/delivery-date-block',
                        'displayArea' => 'delivery-date-block',
                        'deps' => 'checkoutProvider',
                        'dataScopePrefix' => 'delivery_date',
                        'children' => [
                            'form-fields' => [
                                'component' => 'uiComponent',
                                'displayArea' => 'delivery-date-block',
                                'children' => [
                                    'delivery_date' => [
                                        'component' => 'Visy_Checkout/js/view/delivery-date',
                                        'config' => [
                                            'customScope' => 'delivery_date',
                                            'template' => 'ui/form/field',
                                            'elementTmpl' => 'Visy_Checkout/fields/delivery-date',
                                            'options' => [],
                                            'id' => 'delivery_date',
                                            'data-bind' => ['datetimepicker' => true]
                                        ],
                                        'dataScope' => 'delivery_date.delivery_date',
                                        'label' => 'Delivery Date',
                                        'provider' => 'checkoutProvider',
                                        'visible' => true,
                                        'validation' => [
                                            'required-entry' => $requiredDeliveryDate
                                        ],
                                        'sortOrder' => 10,
                                        'id' => 'delivery_date'
                                    ],
                                    'delivery_comment' => [
                                        'component' => 'Magento_Ui/js/form/element/textarea',
                                        'config' => [
                                            'customScope' => 'delivery_date',
                                            'template' => 'ui/form/field',
                                            'elementTmpl' => 'ui/form/element/textarea',
                                            'options' => [],
                                            'id' => 'delivery_comment'
                                        ],
                                        'dataScope' => 'delivery_date.delivery_comment',
                                        'label' => 'Comment',
                                        'provider' => 'checkoutProvider',
                                        'visible' => true,
                                        'validation' => [],
                                        'sortOrder' => 20,
                                        'id' => 'delivery_comment'
                                    ]
                                ],
                            ],
                        ]
                    ]
                ]
            ];
        }

        return $jsLayout;
    }
}
