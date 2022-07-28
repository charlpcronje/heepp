'use strict';

core.get = class {
    constructor() {
        this.method = 'get';
        this.callOptions = {
            callFunc : 'core.console.ui.test',
            callContext : 'core.console.ui',
            callArguments : [],


        };
    }

    static route(route,params = [],callback = undefined,dataType = 'json') {
        var self = this;
        $.ajax({
            url     : core.CONSTANTS.PROJECT.BASE_PATH+route,
            method  : core.get.method,
            data    : $(params).serialize(),
            dataType : dataType
        }).done(function() {
            if (callback !== undefined && callback !== null) {
                eval(callback);
            }
        });
    }

    static asset(asset,type = 'js',callback) {
        var self = this;
        if (type === 'js') {
            $.ajax({
                url     : core.CONSTANTS.PROJECT.PROJECT_ASSETS_PATH+'js/'+asset,
                method  : core.get.method,
                dataType : 'script',
                complete : function() {
                    if (callback !== undefined) {
                       eval(callback);
                    }
                }
            });
        }

        if (type === 'css') {
            $('head').append(`<link href="${core.CONSTANTS.PROJECT.PROJECT_ASSETS_PATH}css/${asset}" type="text/css" rel="stylesheet"/>`);
            $(document).ready(function() {
                eval(callback);
            });
        }
    }

    /* TODO: Finish core.get.callback();
    static objMethod(method,context = window,args = undefined) {
        let callBK = callback.split('.').reduce((o,i) => o[i],window);
        let callContext = context.split('.').reduce((o,i) => o[i],window);
        if (args !== undefined && typeof args === 'object' && args.length > 0) {
            callBK.call(context,args);
        } else {
            callBK.call(context);
        }
    }
     */
};
