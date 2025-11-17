function ajaxBeforeSend(elements) {
    // Fade out website
    // $('#cc').css('opacity',.6);
}

function ajaxSuccess(elements) {
    if (elements.length === 0) {
        elements.push('body');
    }
    $(elements).each(function(i,element) {
        applyUIKitNav(element);
    });
}

function ajaxComplete(elements) {
    /* Fade in website */
    // $('#cc').css('opacity',1);
}

function ajaxError(elements) {
    // $('#cc').css('opacity',1);
}


