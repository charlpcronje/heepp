

core.api = class {
    'use strict';
    constructor(reqType = 'get',reqEndpoint = 'Controller/session',options = {}) {
        /* Set request default */
        this.request = {
            success   : false,
            type      : reqType,
            endPoint  : reqEndpoint,
            userAgent : 'heeppJSCore',
            headers   : {
                authorization : '',
                accept        : 'application/json'
            },
            result      : null,
            response    : null,
            params      : [],
            error       : null,
        };
        this.api = null;
        this.requestOptions = $({},this.request,options);
    }

    params() {

    }

    get() {

    }

    post() {

    }

    put() {

    }

    delete() {

    }

    request() {
        var self = this;
        this.api = $.ajax({
            url     : self.endPoint,
            method  : self.type,
            data    : self.requestOptions,
            beforeSend: function(request) {
                request.setRequestHeader("User-Agent",self.userAgent)
            }
        });
    }

    init() {

    }
};
