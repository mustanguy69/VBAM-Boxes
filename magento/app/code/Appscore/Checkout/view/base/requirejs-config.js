var config = {
    'config': {
        'mixins': {
           'Magento_Checkout/js/view/shipping': {
               'Appscore_Checkout/js/view/shipping-payment-mixin': true
           },
           'Magento_Checkout/js/view/payment': {
               'Appscore_Checkout/js/view/shipping-payment-mixin': true
           }
       }
    }
}