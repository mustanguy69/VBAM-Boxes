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
                $(".navigation__item--parent").unbind().hover(
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
                $(".navigation__item--parent").unbind().click(
                    function(e) {
                        e.stopPropagation();
                        $(this).children('.submenu').stop(true, true).slideToggle('medium');
                    }
                );
                
                $('#maincontent').click(function(e) {
                    if($('label[data-role="minisearch-label"]').hasClass('active')) {
                        $('.page-header').css('height', 'inherit');
                        $('label[data-role="minisearch-label"]').removeClass('active');
                    } 
                });

                $('label[data-role="minisearch-label"]').on('click', function(e){
                    e.preventDefault();

                    if($(this).hasClass('active')) {
                        $('.page-header').css('height', 'inherit');
                        $(this).removeClass('active');
                    } else {
                        $('.page-header').css('height', '150px');
                        $(this).addClass('active');
                    }
                });
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