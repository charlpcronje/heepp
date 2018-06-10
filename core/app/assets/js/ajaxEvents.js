function ajaxBeforeSend(elements) {
    // Fade out website
    // $('#cc').css('opacity',.6);
}

function ajaxSuccess(elements) {
    if (elements.length === 0) {
        elements.push('body');
    }
    $(elements).each(function(i,element) {
        applyNiceScroll(element);
        applyUIKitNav(element);
    });

    /* Apply Resize and Draggable */
    function applyResizeAndDraggable(element) {
        $(element).find('.draggable').getNiceScroll().resize();
    }

    /* Apply UIKit Nav (Left Sub Nav) */
    function applyUIKitNav(element) {
        UIkit.nav($(element).find('.uk-nav'));
    }

    /* Apply Nice Scroll */
    function resizeNiceScroll(element) {
        $(element).find('.nice-scroll').getNiceScroll().resize();
    }

    function applyNiceScroll(element) {
        $(element).find('.nice-scroll').niceScroll({
            cursorcolor: "#101010",             // change cursor color in hex
            cursoropacitymin: 0,                // change opacity when cursor is inactive (scrollabar "hidden" state), range from 1 to 0
            cursoropacitymax: 0.8,              // change opacity when cursor is active (scrollabar "visible" state), range from 1 to 0
            cursorwidth: "5px",                 // cursor width in pixel (you can also write "5px")
            cursorborder: "1px solid #050505",  // css definition for cursor border
            cursorborderradius: "0px",          // border radius in pixel for cursor
            zindex: "auto",                     // | [number],      // change z-index for scrollbar div
            scrollspeed: 60,                    // scrolling speed
            mousescrollstep: 40,                // scrolling speed with mouse wheel (pixel)
            touchbehavior: false,               // DEPRECATED!! use "emulatetouch"
            emulatetouch: false,                // enable cursor-drag scrolling like touch devices in desktop computer
            hwacceleration: true,               // use hardware accelerated scroll when supported
            boxzoom: false,                     // enable zoom for box content
            dblclickzoom: true,                 // (only when boxzoom=true) zoom activated when double click on box
            gesturezoom: true,                  // (only when boxzoom=true and with touch devices) zoom activated when pinch out/in on box
            grabcursorenabled: true,            // (only when touchbehavior=true) display "grab" icon
            autohidemode: true,                 // how hide the scrollbar works, possible values:
                // true                         // hide when no scrolling
                // "cursor"                     // only cursor hidden
                // false                        // do not hide,
                // "leave"                      // hide only if pointer leaves content
                // "hidden"                     // hide always
                // "scroll",                    // show only on scroll
                // background: "",              // change css for rail background
            iframeautoresize: true,             // autoresize iframe on load event
            cursorminheight: 32,                // set the minimum cursor height (pixel)
            preservenativescrolling: true,      // you can scroll native scrollable areas with mouse, bubbling mouse wheel event
            railoffset: false,                  // you can add offset top/left for rail position
            bouncescroll: false,                // (only hw acceleration) enable scroll bouncing at the end of content as mobile-like
            spacebarenabled: true,              // enable page down scrolling when space bar has pressed
            railpadding: {
                top: 2,
                right: 2,
                left: 2,
                bottom: 2
            }, // set padding for rail bar
            disableoutline: true,                   // for chrome browser, disable outline (orange highlight) when selecting a div with nicescroll
            horizrailenabled: false,                // nicescroll can manage horizontal scroll
            railalign: 'right',                     // alignment of vertical rail
            railvalign: 'bottom',                   // alignment of horizontal rail
            enabletranslate3d: true,                // nicescroll can use css translate to scroll content
            enablemousewheel: true,                 // nicescroll can manage mouse wheel events
            enablekeyboard: true,                   // nicescroll can manage keyboard events
            smoothscroll: true,                     // scroll with ease movement
            sensitiverail: true,                    // click on rail make a scroll
            enablemouselockapi: true,               // can use mouse caption lock API (same issue on object dragging)
            cursorfixedheight: false,               // set fixed height for cursor in pixel
            hidecursordelay: 400,                   // set the delay in microseconds to fading out scrollbars
            directionlockdeadzone: 6,               // dead zone in pixels for direction lock activation
            nativeparentscrolling: true,            // detect bottom of content and let parent to scroll, as native scroll does
            enablescrollonselection: true,          // enable auto-scrolling of content when selection text
            cursordragspeed: 0.3,                   // speed of selection when dragged with cursor
            rtlmode: 'auto',                        // horizontal div scrolling starts at left side
            cursordragontouch: false,               // drag cursor in touch / touchbehavior mode also
            oneaxismousemode: 'auto',               // it permits horizontal scrolling with mousewheel on horizontal only content, if false (vertical-only) mouse-wheel don't scroll horizontally, if value is auto detects two-axis mouse
            scriptpath: '',                         // define custom path for box mode icons ("" => same script path)
            preventmultitouchscrolling: true,       // prevent scrolling on multi-touch events
            disablemutationobserver: false,         // force MutationObserver disabled,
            enableobserver: true,                   // enable DOM changing observer, it tries to resize/hide/show when parent or content div had changed
            scrollbarid: false                      // set a custom ID for nicescroll bars
        });
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


