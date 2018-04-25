function ajaxBeforeSend(elements) {
    // Fade out website
    // $('#cc').css('opacity',.6);
}

function ajaxSuccess(elements) {
    $(elements).each(function(i,element) {
        applyNiceScroll(element);
        applyUIkitNav(element);
        // applyResizeAndDraggable(element);
    });

    /* Apply Resize and Draggable */
    function applyResizeAndDraggable(element) {
        $(element).find('.draggable').getNiceScroll().resize();
    }

    /* Apply UIKit Nav (Left Sub Nav) */
    function applyUIkitNav(element) {
        UIkit.nav($(element).find('.uk-nav'));
    }

    /* Apply Tooltips */
    function applyTooltips(element) {
        $(element+' [data-toggle="tooltip"]').tooltip();
    }

    /* Apply Nice Scroll */
    function resizeNiceScroll(element) {
        $(element).find('.nice-scroll').getNiceScroll().resize();
    }
    function applyNiceScroll(element) {
        $(element).find('.nice-scroll').niceScroll();
        $(window).resize(function () {
            resizeNiceScroll(element);
        });
    }
}

function ajaxComplete(elements) {
    /* Fade in website */
    // $('#cc').css('opacity',1);
}

function ajaxError(elements) {
    // $('#cc').css('opacity',1);
}


