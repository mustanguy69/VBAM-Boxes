define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
],
function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'appscore_securepay',
            component: 'Appscore_SecurePay/js/view/payment/method-renderer/securepay'
        }
    );

    /** Add view logic here if needed */
    return Component.extend({});
});