'use strict';

var loadedCoreCSS = [];

core.event.ready = class {
    static load(elem,url) {
        var file = (/[.]/.exec(url)) ? /[^.]+$/.exec(url) : undefined;
        switch(file[0]) {
            case 'js':
                $.ajax({
                    url: url,
                    dataType: "script",
                    success: function() {
                        init();
                    },
                    beforeSend: false
                });
            break;
            case  'css':
                if (!loadedCoreCSS.includes(url)) {
                    loadedCoreCSS.push(url);
                    $("<link/>", {
                        rel: "stylesheet",
                        type: "text/css",
                        href: url
                    }).appendTo("head");
                }
            break;
        }
        if ($(elem)[0].nodeName === 'JSCRIPT' || $(elem)[0].nodeName === 'CSS') {
            $(elem).remove();
        }
    }
};
