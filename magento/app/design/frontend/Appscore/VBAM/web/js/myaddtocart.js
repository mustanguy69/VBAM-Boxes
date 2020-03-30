require([
    'jquery'
], function ($, config) {
    "use strict";

    $(document).ready(function(){
        var $window = $(window);

        function setInput(e, add = true) {
            // get parent
            var parent = e.parents('.product-item-info, .product-info');

            // get input field
            var inputField = parent.find('.input-text.qty');

            var nextVal = 1;

            if(add === true) {
                nextVal = parseInt(inputField.val()) + 1;
            } else {
                nextVal = parseInt(inputField.val()) - 1;
                if(nextVal <= 0) {
                    nextVal = 1;
                }
            }
            
            inputField.val(nextVal);
        }

        $(document).on('click', '.qty-controller.qty-minus', function () {
            setInput($(this), false);
        });

        $(document).on('click', '.qty-controller.qty-plus', function () {
            setInput($(this), true);
        });

        $(document).on('blur', '.input-text.qty', function () {
            var parent = $(this).parents('.product-item-info, .product-info');
            var inputField = parent.find('.input-text.qty');

            if(parseInt($(this).val()) <= 0) {
                inputField.val(1);
            } else {
                inputField.val($(this).val());
            }
        });

        $(document).on('click', '.opentocart', function() {
            var windowsize = $window.width();
            var parent = $(this).parents('.product-item-info');
            var mobileContainer = parent.find('.product-item-cart-quantity');
            var realAddToCartBtn = parent.find('.actions-primary .tocart');

            if (windowsize <= 768) {
                if ($(this).width() + 30 < $(this).parent().width()) {
                    $(this).animate({
                        width: '100%'
                    }, 200, function() {
                        $(this).text('ADD TO CART');
                    });

                    mobileContainer.fadeIn('fast');
                } else {
                    realAddToCartBtn.trigger('click');
                }
            } 
        });

        $(document).on('click', '.qty-close', function() {
            var windowsize = $window.width();
            var parent = $(this).parents('.product-item-info');
            var cartContainer = parent.find('.product-item-cart-mobile');
            var mobileContainer = parent.find('.product-item-cart-quantity');

            var openToCart = parent.find('.opentocart');
            var cartWrapper = cartContainer.find('.cart-wrapper');

            if (windowsize <= 768) {
                if (openToCart.width() + 30 >= cartWrapper.width()) {
                    openToCart.animate({
                        width: '35px'
                    }, 200, function() {
                        var image = openToCart.attr('image-bind');
                        openToCart.html('<img src="'+image+'" width="8" alt="plus"/>');
                    });
                    mobileContainer.fadeOut('fast');
                }
            } 
        });
    });
});