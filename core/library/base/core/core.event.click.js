'use strict';

core.event.click = class {
    static load(elem,url) {
        $(elem).click(function(event) {
            event.preventDefault();
            $.ajax({
                url : url
            });
        });
    }
};
