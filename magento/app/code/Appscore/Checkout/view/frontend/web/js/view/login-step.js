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

        /**
         * check-login - is the name of the component's .html template
         */
        return Component.extend({
            isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
            isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
            registerUrl: checkoutConfig.registerUrl,
            forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
            autocomplete: checkoutConfig.autocomplete,
            defaults: {
                template: 'Appscore_Checkout/login'
            },

            //add here your logic to display step,
            isVisible: ko.observable(true),
            isLoggedIn: function () {
                return customer.isLoggedIn();
            },
            //step code will be used as step content id in the component template
            stepCode: 'loginStep',
            //step title value
            stepTitle: 'CHECKOUT',

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    this.stepCode,
                    //step alias
                    null,
                    this.stepTitle,
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),

                    /**
                     * sort order value
                     * 'sort order value' < 10: step displays before shipping step;
                     * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                     * 'sort order value' > 20 : step displays after payment step
                     */
                    1
                );

                if(customer.isLoggedIn())
                {
                    ko.observable(false);
                    stepNavigator.next();
                }
                

                $(document).ready(function () {
                    var guestSelected = 0;
                    $(document).on('change','input[name="login"]', function(){
                        if ($(this).val() == 'yes') {
                            $('#login-form').css('display', 'block');
                            $('#guest-form, #shipping, #opc-shipping_method').css('display', 'none');
                        } else if($(this).val() == 'no') {
                            $('#login-form').css('display', 'none');
                            $('#guest-form').css('display', 'block');
                        }
                    });

                    $(document).on('click', '#guest-form button', function () {
                        guestSelected = 1;
                        $('#login-choice').css('display', 'none');
                        $('#login-form').css('display', 'none');
                    })

                    if(guestSelected == 1) {
                        $('#login-choice').css('display', 'none');
                        $('#login-form').css('display', 'none');
                    }
                });

                return this;
            },

            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            navigate: function () {

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
