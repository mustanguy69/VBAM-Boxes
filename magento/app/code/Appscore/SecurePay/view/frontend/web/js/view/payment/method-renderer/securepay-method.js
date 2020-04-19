define([
    'jquery',
    'Magento_Checkout/js/action/place-order',
    'Magento_Payment/js/view/payment/cc-form',
    'Magento_Checkout/js/action/redirect-on-success'
],
function ($, placeOrderAction, Component, redirectOnSuccessAction) {
    'use strict';

    var mySecurePayUI;
    return Component.extend({
        defaults: {
            template: 'Appscore_SecurePay/payment/securepay',
        },

        initialize: function () {
            this._super();

            $(document).ready(function() {
                $(window).bind('hashchange', function () { 
                    var hash = window.location.hash.slice(1);
                    if (hash == "payment") {
                        var config = JSON.parse(window.checkoutConfig.payment.securepay);
                        
                        var availablePayment = window.checkoutConfig.payment.ccform.availableTypes.appscore_securepay
                        var availablePaymentTab =  []
                        $.each(availablePayment, function( index, value ) {
                            availablePaymentTab.push(value.toLowerCase());
                        });
                        
                        if($('#securepay-ui-iframe-0').length == 0) {
                            mySecurePayUI = new securePayUI.init({
                                containerId: 'securepay-ui-container',
                                scriptId: 'securepay-ui-js',
                                clientId: ''+config.client_id+'',
                                merchantCode: ''+config.merchant_code+'',
                                card: {
                                    allowedCardTypes: availablePaymentTab,
                                    onTokeniseSuccess: function(tokenisedCard) {
                                        
                                    $('input[name="token"]').val(tokenisedCard.token);
                                    $('.place-order').eq(0).click();
                                    // card was successfully tokenised
                                    // here you could make a payment using the SecurePay API (via your application server)
                                    },
                                    onTokeniseError: function(errors) {
                                        console.log(errors);
                                    // error while tokenising card 
                                    }
                                },
                                style: {
                                    backgroundColor: '#F7F5F5',
                                    label: {
                                    font: {
                                        family: 'Arial, Helvetica, sans-serif',
                                        color: '#0C2340'
                                    }
                                    },
                                    input: {
                                    font: {
                                        family: 'Arial, Helvetica, sans-serif',
                                        color: 'black'
                                    }
                                }  
                                }, 
                            });
                        }
                        
                    }
                });
            });

            return this;
        },

        context: function() {
            return this;
        },

        getCode: function() {
            return 'appscore_securepay';
        },

        isActive: function() {
            return true;
        },

        /**
         * Get data
         * @returns {Object}
         */
        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'token': $('input[name="token"]').val(),
                }
            };
        },

        placeOrderOverrided: function () {
            mySecurePayUI.tokenise();
        },

        placeOrder: function (data, event) {
            var self = this;
        
            if (event) {
                event.preventDefault();
            }
        
            
            this.isPlaceOrderActionAllowed(false);
    
            this.getPlaceOrderDeferredObject()
                .fail(
                    function () {
                        self.isPlaceOrderActionAllowed(true);
                    }
                ).done(
                    function () {
                        self.afterPlaceOrder();
    
                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    }
                );
    
            return true;
            
        },

        getPlaceOrderDeferredObject: function () {
            return $.when(
                placeOrderAction(this.getData(), this.messageContainer)
            );
        },

    });
}
);