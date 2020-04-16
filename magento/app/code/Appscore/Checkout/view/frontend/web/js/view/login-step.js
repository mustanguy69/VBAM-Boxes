define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/model/customer',
        'jquery',
        'Magento_Customer/js/action/login',
        'mage/validation',
        'Magento_Checkout/js/model/authentication-messages',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        ko,
        Component,
        _,
        stepNavigator,
        customer,
        $,
        loginAction,
        validation,
        messageContainer,
        fullScreenLoader,
        hello
    ) {
        'use strict';

        var checkoutConfig = window.checkoutConfig;

        return Component.extend({
            isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
            isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
            registerUrl: checkoutConfig.registerUrl,
            forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
            autocomplete: checkoutConfig.autocomplete,
            defaults: {
                template: 'Appscore_Checkout/login'
            },

            isVisible: ko.observable(true),
            isLoggedIn: function () {
                return customer.isLoggedIn();
            },
            stepCode: 'login-choice',
            stepTitle: 'CHECKOUT',

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();

                stepNavigator.registerStep(
                    this.stepCode,
                    null,
                    this.stepTitle,
                    this.isVisible,

                    _.bind(this.navigate, this),
                    1
                );

                if(customer.isLoggedIn())
                {
                    ko.observable(false);
                    stepNavigator.next();
                }
                

                // $(document).ready(function () {
                //     $(document).on('change','input[name="login"]', function(){
                //         if ($(this).val() == 'yes') {
                //             $('#login-form').css('display', 'block');
                //             $('#guest-form, #shipping, #opc-shipping_method').css('display', 'none');
                //         } else if($(this).val() == 'no') {
                //             $('#login-form').css('display', 'none');
                //             $('#guest-form').css('display', 'block');
                //         }
                //     });
                // });


                return this;
            },

            navigate: function () {

                this.isVisible(true);
            },

            /**
             * @returns void
             */
            navigateToNextStep: function () {
                stepNavigator.next();
            },

            login: function (loginForm) {
                var loginData = {},
                    formDataArray = $(loginForm).serializeArray();

                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });

                if ($(loginForm).validation() &&
                    $(loginForm).validation('isValid')
                ) {
                    fullScreenLoader.startLoader();
                    loginAction(loginData, checkoutConfig.checkoutUrl, undefined, messageContainer).always(function () {
                        fullScreenLoader.stopLoader();
                    });
                }
            },
        });
    }
);
