define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/model/customer',
        'mage/validation',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/set-shipping-information',
        'mage/calendar',
    ],
    function (
        $,
        ko,
        Component,
        _,
        stepNavigator,
        customer,
        validation,
        quote,
        fullScreenLoader,
        setShippingInformationAction,
    ) {
        'use strict';

        var checkoutConfig = window.checkoutConfig;

        return Component.extend({
            isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
            isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
            registerUrl: checkoutConfig.registerUrl,
            autocomplete: checkoutConfig.autocomplete,
            defaults: {
                template: 'Appscore_Checkout/delivery-date'
            },

            isVisible: ko.observable(quote.isVirtual()),
            isLoggedIn: function () {
                return customer.isLoggedIn();
            },
            stepCode: 'delivery-date',
            stepTitle: 'SHIPPING',

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
                    16
                );

                $(document).ready(function() {
                    $(window).bind('hashchange', function () { 
                        var hash = window.location.hash.slice(1);
                        if (hash == "delivery-date") {
                            
                            if(quote.isDelivery == false) {
                                stepNavigator.next()
                            }
                            var dayPrior = window.checkoutConfig.shipping.delivery_date.minDate;
                            var d = new Date();
                            d.setDate(d.getDate() + parseInt(dayPrior));
                            $('#datepicker').calendar({
                                changeMonth: true,
                                changeYear: true,
                                showButtonPanel: false,
                                showWeek: false,
                                minDate: d,
                            });

                            $(document).on('click', '#btn-calendar',function () {
                                if($("#ui-datepicker-div").css('display')  == 'block') {
                                    $("#ui-datepicker-div").css({'display': 'none', 'position': 'absolute'});
                                } else {
                                    $("#ui-datepicker-div").css({'display': 'block', 'position': 'absolute'});
                                }
                                
                            })

                            $(document).on('click', '#ui-datepicker-div td a',function () {
                                if($("#ui-datepicker-div").css('display')  == 'block') {
                                    $("#ui-datepicker-div").css({'display': 'none', 'position': 'absolute'});
                                } else {
                                    $("#ui-datepicker-div").css({'display': 'block', 'position': 'absolute'});
                                }
                                
                            })

                            $(document).on('click', '#datepicker', function() {
                                $('#delivery-date .error').css('display', 'none');
                                $('#datepicker').css('border', 'none');
                            });
                        }
                    });
                    
                });

                return this;
            },

            navigate: function () {
                var self = this;
                self.visible(true);
            },

            /**
             * @returns void
             */
            navigateToNextStep: function () {
                stepNavigator.next();
            },

            saveDeliveryDate: function () {
                if(quote.isDelivery && $('#datepicker').val() == '') {
                    $('#delivery-date .error').css('display', 'block');
                    $('#datepicker').css('border', '1px solid red');
                } else {
                    fullScreenLoader.startLoader();
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                    fullScreenLoader.stopLoader();
                }
            }
        });
    }
);
