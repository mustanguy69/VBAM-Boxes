define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t
    ) {
        'use strict';
    
        return Component.extend({
            defaults: {
                template: 'Appscore_Checkout/shipping-methods'
            },
    
            //add here your logic to display step,
            isVisible: ko.observable(true),
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isFormInline: addressList().length == 0,
    
            /**
            *
            * @returns {*}
            */
            initialize: function () {
                this._super();
    
                stepNavigator.registerStep(
                    //step code will be used as step content id in the component template
                    'shipping_methods',
                    //step alias
                    null,
                    //step title value
                    'Shipping Methods',
                    //observable property with logic when display step or hide step
                    this.isVisible,
    
                    _.bind(this.navigate, this),
    
                    17
                );

                $(document).ready(function() {
                    $(document).on('click', '.delivery', function () {
                        $('.table-checkout-shipping-method').css('display', 'block');
                        $('.delivery').removeClass('green-button-empty').addClass('green-button-full');
                        $('.clickandcollect').removeClass('green-button-full').addClass('green-button-empty');
                        $('.clickandcollect-container').css('display', 'none');
                    });

                    $(document).on('click', '.clickandcollect', function () {
                        $('.table-checkout-shipping-method').css('display', 'none');
                        $('.delivery').removeClass('green-button-full').addClass('green-button-empty');
                        $('.clickandcollect').removeClass('green-button-empty').addClass('green-button-full');
                        $('.clickandcollect-container').css('display', 'block');
                    });

                    $(document).on('click', '#shipping-method-buttons-container', function() {
                        $('#co-shipping-method-form').submit();
                    });
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
    
    
            rates: shippingService.getShippingRates(),
            isLoading: shippingService.isLoading,
    
            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
            },
    
            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();
    
                if (!quote.shippingMethod()) {
                    this.errorValidationMessage('Please specify a shipping method.');
    
                    return false;
                }
    
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
    
                if (this.isFormInline) {
    
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');
    
                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }
    
                    if (this.source.get('params.invalid') ||
                        !quote.shippingMethod().method_code ||
                        !quote.shippingMethod().carrier_code ||
                        !emailValidationResult
                    ) {
                        return false;
                    }
    
                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );
    
                    //Copy form data to quote shipping address object
                    for (var field in addressData) {
    
                        if (addressData.hasOwnProperty(field) &&
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }
    
                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);
                }
    
                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();
    
                    return false;
                }
    
                return true;
            }
    
    
        });
    }
    );