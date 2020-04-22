define(
    [
        'jquery',
        'mage/url',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'loader',
        'Magento_Checkout/js/model/full-screen-loader',
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
        url,
        _,
        Component,
        ko,
        loader,
        fullScreenLoader,
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
            isVisible: ko.observable(quote.isVirtual()),
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
    
                    15
                );

                

                $(document).ready(function() {
                    $(window).bind('hashchange', function () { 
                        var hash = window.location.hash.slice(1); //hash to string (= "myanchor")
                        if (hash == "payment") {
                            console.log(quote);
                        }
                        if (hash == "shipping_methods") {
                            var firstCharge = true;
                            if(firstCharge) {
                                $("body").one('processStop', function() {
                                    $('.delivery').eq(0).click();
                                    firstCharge = false;
                                });
                            }
                            $(document).on('click', '.delivery', function () {
                                quote.isDelivery = true;
                                $('#checkout-shipping-method-load').css('display', 'block');
                                $('.delivery').removeClass('green-button-empty').addClass('green-button-full');
                                $('.clickandcollect').removeClass('green-button-full').addClass('green-button-empty');
                                $('.clickandcollect-container').css('display', 'none');
                            });
        
                            $(document).on('click', '.clickandcollect', function () {
                                quote.isDelivery = false;
                                $('#checkout-shipping-method-load').css('display', 'none');
                                $('.delivery').removeClass('green-button-full').addClass('green-button-empty');
                                $('.clickandcollect').removeClass('green-button-empty').addClass('green-button-full');
                                $('.clickandcollect-container').css('display', 'block');
                            });
                            
                            $(document).on('click', '#search-branch-button', async function() {
                                showLoading();
                                var value = $('#search-branch').val();
                                var urlAjax = url.build('branchlocator/index/search');
                                var apikey = await getApiKeyGmaps();
                                var pos = await getPos(apikey, value);
                                $.ajax({
                                    url: urlAjax,
                                    method: 'POST',
                                    dataType: 'json',
                                    data: pos,
                                }).done(function (branches) {
                                    branches.sort(function(obj1, obj2) {
                                        return obj1.distance - obj2.distance;
                                    });
                                    
                                    var html = "";
                                    if (branches.length != 0) {
                                        $.each(branches, function(key, value) {
                                            if (value.distance < 200) {
                                                html = html + '<div class="search-result">' +
                                                '<h1>'+value.name+'</h1>'+
                                                '<button data-id="'+ value.id +'" class="button select-store action continue primary green-button-empty" id="select-store">SELECT THIS STORE</button>' +
                                                '<p>'+value.address+', '+value.city+', '+value.state+', '+value.postcode+'</p>'+
                                                '<p class="away"><span class="km">'+ Math.floor(value.distance) +'</span> km away</p>'+
                                                '</div>';
                                            }
                                        });
                                    } else {
                                        html = "<p style='margin-top: 10px; text-align:center'>No store found</p>"
                                    }
                                    
                                    $('.search-result-container').html(html);
                                    hideLoading();
                                });
                                
                            });

                            $(document).on('click', '.select-store', function () {
                                $('.select-store').attr('id', '').removeClass('green-button-full').addClass('green-button-empty').text('SELECT THIS STORE')
                                $(this).attr('id', 'selected-store').removeClass('green-button-empty').addClass('green-button-full').text('SELECTED')
                                
                            })
                            
                            
                            $(document).on('click', '.search-icon', async function () {
                                showLoading();
                                var apikey = await getApiKeyGmaps();
                                getCurrentPos(apikey);
                                hideLoading();
                            })

                            function getApiKeyGmaps() {
                                return new Promise(resolve => {
                                    var urlAjaxApiKey = url.build('branchlocator/index/apikey');
                                    $.ajax({
                                        url: urlAjaxApiKey,
                                        method: 'POST',
                                        dataType: 'json',
                                    }).done(function (apikey) {
                                        resolve(apikey);
                                    });
                                });
                            }
                              
                            function getPos(apikey, address) {
                                return new Promise(resolve => {
                                    require([ 'https://maps.googleapis.com/maps/api/js?key='+apikey ], function () {

                                        var pos = {};
                                        var geocoder= new google.maps.Geocoder();
                                        geocoder.geocode({'address': address, 'componentRestrictions':{'country':'au'}}, function(results, status){
                                            if (status == google.maps.GeocoderStatus.OK) {
                                                pos = {
                                                    lat: results[0].geometry.location.lat(),
                                                    lng: results[0].geometry.location.lng()
                                                }

                                                resolve(pos);
                                                
                                            }
                                        });
                                    });  
                                });
                                
                            }

                            function getCurrentPos(apikey) {
                                require([ 'https://maps.googleapis.com/maps/api/js?key='+apikey ], function () {
                                    var pos = {};
                                    var geocoder= new google.maps.Geocoder();
                                    $('#example').loader({
                                        icon: 'path to icon'
                                    });
                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function(position) {
                                            if(position.coords.latitude !== "undefined" && position.coords.longitude !== "undefined") {
                                                pos = {
                                                    lat: position.coords.latitude,
                                                    lng: position.coords.longitude
                                                };

                                                geocoder.geocode({'latLng': pos}, function(results, status){
                                                    if (status == google.maps.GeocoderStatus.OK) {
                                                        $('#search-branch').val(results[0].formatted_address);
                                                    }
                                                });

                                            } 
                                        }, function (error) {
                                            alert('Please active your geolocation');
                                        });
                                    }
                                });
                            }

                           function showLoading() {
                                $('.clickandcollect-container').trigger('processStart');
                           }

                           function hideLoading() {
                                $('.clickandcollect-container').trigger('processStop');
                           }
                        }
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
                var self = this;
                self.visible(true);
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
            },

            getShippingAddress: function () {
                return quote.shippingAddress();
            },

            getCountryName: function () {
                var country = "";
                if(quote.shippingAddress().countryId == "AU") {
                    country = "Australia";
                } else if (quote.shippingAddress().countryId == "NZ") {
                    country = "New Zealand"
                }

                return country;
            },

            saveMethods: function () {
                fullScreenLoader.startLoader();
                setShippingInformationAction().done(
                    function () {
                        stepNavigator.next();
                    }
                );
                fullScreenLoader.stopLoader();
            },

                /**
             * Shipping Method View
             */
            rates: shippingService.getShippingRates(),
            isLoading: shippingService.isLoading,
            isSelected: ko.computed(function () {
                return quote.shippingMethod() ?
                    quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                    null;
            }),

            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code']);

                return true;
            }

            
        });
    }
    );