define([
        'jquery'
    ], function($){

        "use strict";

        $(document).ready(function($){
            var $window = $(window);
            
            function checkWidthForNavigation() {
                var windowsize = $window.width();
                if (windowsize > 768) {
                    parentHover();
                } else {
                    parentClick();
                }
            }

            function parentHover() {
                $(".navigation__item--parent").prop("onclick", null).off("click");
                $(".navigation__item--parent").hover(
                    function() {
                        $(this).children('.submenu').stop(true, true).fadeIn('medium');
                    },
                    function() {
                        $(this).children('.submenu').stop(true, true).fadeOut('medium');
                    }
                );
            }

            function parentClick() {
                $(".navigation__item--parent").unbind('mouseenter mouseleave');
                $(".navigation__item--parent").click(
                    function() {
                        $(this).children('.submenu').stop(true, true).slideToggle('medium');
                    }
                );
            }

            function clickToCloseMobileMenu() {
                $('.close-menu').click(
                    function() {
                        $('html').removeClass('nav-open');

                        //timeout
                        setTimeout(function() {
                            $('html').removeClass('nav-before-open');
                        }, 200)
                    }
                )
            }
            
            function hello() {
                $('#member').on('click', function() {
                    console.log('test');
                });
            }
            
            
            // checking for window resize
            $(window).resize(function() {
                checkWidthForNavigation();
            });

            // initial load
            checkWidthForNavigation();
            clickToCloseMobileMenu();

        });
        return;
});