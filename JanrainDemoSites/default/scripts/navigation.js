/*
 * Let the dropdown arrows behave kind of like an accordian nav should, by
 * intercepting clicks on the arrow icon before passing them through to the
 * link.
 */
jQuery(document).ready(function($) {
    $('.dropdown > a:first-child').on('click', function(event) {
        var clickedLoc = event.clientX,
            handleWidth = $(this).width(),
            hasLink = ( $(event.currentTarget).attr('href') != undefined );

        if ( handleWidth - clickedLoc < 48 || !hasLink ) {
            $(event.delegateTarget).closest('.dropdown').toggleClass('expanded');
            return false;
        }
    });

    $('.sidebar_nav a').each(function(i, elt) {
        if ( $(elt).attr('href') === location.pathname ) {
            $(elt).closest('li').addClass('selected');
        }
    });

    $('[role="toggle-sidebar-nav"]').on('click', function() {
        $('body').toggleClass('focused-mobile-nav');
        $('[role="toggle-sidebar-nav"]').toggleClass('active');

        $('.sidebar_col').on('click', function() {
            $('[role="toggle-sidebar-nav"]').trigger('click');
        });
    });

    $('[role="toggle-global-nav"]').on('click', function() {
        $('body').toggleClass('focused-global-nav');
        $('[role="toggle-global-nav"]').toggleClass('active');

        $('.global_nav').on('click', function() {
            $('[role="toggle-global-nav"]').trigger('click');
        });
    });
});
