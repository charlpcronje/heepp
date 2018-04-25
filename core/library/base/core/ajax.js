'use strict';

function ajaxSuccessEvents(objs) {
    if (typeof objs == 'number') {
        return false;
    }
    $.each(objs,function (obj,args) {
        switch(obj) {
            case 'notify':
                var notify = new core.notify();
                notify.init(args['type'],args['message']);
            break;
            case 'redirect':
                $.ajax({
                    url : args['url'],
                    type : args['method']
                });
            break;
            case 'html':
                var html = new core.html();
                $(args).each(function (i,arg) {
                    html.init(arg['method'],arg['target'],arg['html']);
                    core.addUISelector(arg['target']);
                });
            break;
            case 'value':
                var value = new core.value();
                $(args).each(function (i,arg) {
                    value.init(arg['target'],arg['value']);
                });
            break;
            case 'offcanvas':
                var offcanvas = new core.offcanvas();
                $(args).each(function (i,arg) {
                    offcanvas.init(arg['heading'],arg['body'],arg['width']);
                    core.addUISelector('#' + offcanvas.uniqueId);
                });
            break;
            case 'click':
                $(args).each(function (i,arg) {
                    $(arg['selector']).click();
                });
                break;
            case 'var':
                $(args).each(function (i,arg) {
                    core.strToDot(eval(arg['context']),arg['variable'],arg['value']);
                });
            break;
            case 'callback':
                $(args).each(function (i,arg) {
                    // Gets the actual function object from the dot notation string inside arg['callback']
                    let callback = arg['callback'].split('.').reduce((o,i) => o[i],window);
                    // Gets the actual context object from the dot notation string inside arg['context']
                    let context = arg['context'].split('.').reduce((o,i) => o[i],window);
                    callback.call(context,arg['arguments']);
                    /* So basically call must look like:
                     * callback = core.app.dashboard.setPanelWith (function)
                     * callback.call(core.app.dashboard) - So the context is the class the callback must run in */
                });
            break;
            case 'console':
                $(args).each(function (i,arg) {
                    console.log({
                        description : arg['description'],
                        data        : arg['data']
                    })
                });
            break;
            case 'class':
                $(args).each(function (i,arg) {
                    if (arg['method'] == 'add') {
                        $(arg['target']).addClass(arg['class']);
                    } else if (arg['method'] == 'remove') {
                        $(arg['target']).removeClass(arg['class']);
                    }
                });
            break;
            case 'style':
                $(args).each(function (i,arg) {
                    $(arg['target']).css(arg['style'],arg['value']);
                });
            break;
            case 'attr':
                $(args).each(function (i,arg) {
                    $(arg['target']).attr(arg['attr'],arg['value']);
                });
            break;
            case 'confirm':
                let confirm = new core.confirm();
                confirm.init(args['heading'],args['message'],args['action']);
            break;
            case 'hide':
                $(args).each(function (i,arg) {
                    $(arg['target']).hide();
                });
            break;
            case 'show':
                $(args).each(function (i,arg) {
                    $(arg['target']).show();
                });
            break;
            default:
                /* console.log("Action for '"+obj+"' not specified"); */
            break;
        }
    });
}

function setAjaxSetup() {
    $.ajaxSetup({
        /* A Boolean value indicating whether the request should be handled
         asynchronous or not. Default is true */
        async       : true,
        /* A function to run before the request is sent */
        beforeSend  : function () {
            if (typeof ajaxBeforeSend == 'function') {
                ajaxBeforeSend(core.getUISelectors());
            }
            if (core.ELEMENTS.loader !== undefined) {
                core.ELEMENTS.loader.show();
            } else {
                core.ELEMENTS.loader = new core.loader('','');
                core.ELEMENTS.loader.init();
            }
        },
        /* A Boolean value indicating whether the browser should cache the
           requested pages. Default is true */
        cache       : true,
        /* A function to run when the request is finished (after success and error
           functions) */
        complete    : function () {
            if (typeof ajaxComplete == 'function') {
                ajaxComplete(core.getUISelectors());
            }
            if ($.active <= 1) {
                var loader = new core.loader();
                loader.hide();
            }
        },
        /* The content type used when sending data to the server. Default is:
           application/x-www-form-urlencoded" */
        contentType : "application/x-www-form-urlencoded",
        /* Specifies data to be sent to the server */
        data        : {},
        /* The data type expected of the server response. */
        dataType    : 'json',
        /* A function to run if the request fails. */
        error       : function (xhr,status,error) {
            if (typeof ajaxError == 'function') {
                ajaxError(core.getUISelectors());
            }
            var notify = new core.notify();
            notify.init('error',error);
            if ($.active <= 1) {
                var loader = new core.loader();
                loader.hide();
            }
        },
        /* A Boolean value specifying whether or not to trigger global AJAX event
           handles for the request. Default is true */
        global      : true,
        /* A Boolean value specifying whether a request is only successful if the
           response has changed since the last request. Default is: false. */
        success     : function (objs,status,xhr) {
            // Do not parse javascript or css files
            if ($.inArray(this.url.split('.').pop(),['js','css']) != -1) {
                return;
            }
            core.clearUISelectors();
            ajaxSuccessEvents(objs);
            core.ajaxSuccess();
            if (typeof ajaxSuccess == 'function') {
                ajaxSuccess(core.getUISelectors());
            }
            core.clearUISelectors(core.getUISelectors());
        },
        /* Specifies the type of request. (GET or POST) */
        type : 'GET'
    });
}
setAjaxSetup();