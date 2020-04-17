define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form'
],
function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Appscore_SecurePay/payment/securepay'
        },

        context: function() {
            return this;
        },

        getCode: function() {
            return 'appscore_securepay';
        },

        isActive: function() {
            return true;
        }
    });
}
);