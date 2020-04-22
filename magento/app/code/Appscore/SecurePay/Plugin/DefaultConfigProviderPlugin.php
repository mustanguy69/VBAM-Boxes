<?php

namespace Appscore\SecurePay\Plugin;

class DefaultConfigProviderPlugin
{

    protected $_scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) 
    { 
       
        $this->_scopeConfig = $scopeConfig;

    }

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $output) 
    {

        $merchantCode = $this->_scopeConfig->getValue(
            'payment/appscore_securepay/merchant_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $clientId = $this->_scopeConfig->getValue(
            'payment/appscore_securepay/client_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $clientSecret = $this->_scopeConfig->getValue(
            'payment/appscore_securepay/client_secret',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $output['payment']['securepay'] = json_encode(array('merchant_code'=> $merchantCode, 'client_id' => $clientId));
    
        return $output;
    }

} 