//----------Heepp Object----------

/*global $,document */
/*jshint -W043 */
/*jslint browser: true*/
'use strict';
const log = console.log;

window.console.log = log;

var core = {
    CONSTANTS : UI_CONSTANTS,
    ZIP_HTML : false,
    ELEMENTS : {},
    UISelectors : new Array('body'),
    addUISelector : function(selector) {
        this.UISelectors.push(selector);
    },
    clearUISelectors : function() {
        this.UISelectors = [];
    },
    applyCoreClasses : function(elem) {
        $.each(elem.attributes,function() {
            if (typeof this !== "undefined") {
                if (this.name.substr(0, 5) == 'core.') {
                    var attrName = this.name;
                    var attrValue = this.value;
                    $(elem).removeAttr(attrName);
                    eval(attrName+'(elem,attrValue)');
                }
            }
        });
    },
    getUISelectors : function() {
        return this.UISelectors;
    },
    ajaxSuccess : function(uiSelectors) {
        self = this;
        $(self.UISelectors).each(function(i,selector) {
            $(selector+' *').each(function(i,elem) {
                self.applyCoreClasses(elem);
            });
        });
    },
    // Support function for setVar and setCallback, this function set dot notation from string and can also set
    // a value of an object property. Ex: var test = 'core.app.dashboard.prop'; setOrGetVar(window,test,50);
    // The example will make core.app.dashboard.prop = 50;
    strToDot : function(obj,is, value) {
        if (typeof is == 'string')
            return this.strToDot(obj,is.split('.'), value);
        else if (is.length==1 && value!==undefined)
            return obj[is[0]] = value;
        else if (is.length==0)
            return obj;
        else
            return this.strToDot(obj[is[0]],is.slice(1), value);
    }
};

$(document).ready(function() {
    core.ajaxSuccess();
});

//----------Heepp Loader Object----------

'use strict';

core.loader = class {
    constructor(heading,message) {
        this.alive = true;
        this.drawn = false;
        this.timeTillDestroy = 15000;
        this.gearColors = {
            primary : '#222',
            secondary : '#161616' //#D1047B
        };
        this.loaderImage       = `<?xml version="1.0" encoding="utf-8"?>
                                      <svg width='136px' height='136px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-gears">
                                          <rect x="0" y="0" width="100" height="100" fill="none" class="bk">
                                          </rect>
                                          <g transform="translate(-20,-20)">
                                              <path d="M79.9,52.6C80,51.8,80,50.9,80,50s0-1.8-0.1-2.6l-5.1-0.4c-0.3-2.4-0.9-4.6-1.8-6.7l4.2-2.9c-0.7-1.6-1.6-3.1-2.6-4.5 L70,35c-1.4-1.9-3.1-3.5-4.9-4.9l2.2-4.6c-1.4-1-2.9-1.9-4.5-2.6L59.8,27c-2.1-0.9-4.4-1.5-6.7-1.8l-0.4-5.1C51.8,20,50.9,20,50,20 s-1.8,0-2.6,0.1l-0.4,5.1c-2.4,0.3-4.6,0.9-6.7,1.8l-2.9-4.1c-1.6,0.7-3.1,1.6-4.5,2.6l2.1,4.6c-1.9,1.4-3.5,3.1-5,4.9l-4.5-2.1 c-1,1.4-1.9,2.9-2.6,4.5l4.1,2.9c-0.9,2.1-1.5,4.4-1.8,6.8l-5,0.4C20,48.2,20,49.1,20,50s0,1.8,0.1,2.6l5,0.4 c0.3,2.4,0.9,4.7,1.8,6.8l-4.1,2.9c0.7,1.6,1.6,3.1,2.6,4.5l4.5-2.1c1.4,1.9,3.1,3.5,5,4.9l-2.1,4.6c1.4,1,2.9,1.9,4.5,2.6l2.9-4.1 c2.1,0.9,4.4,1.5,6.7,1.8l0.4,5.1C48.2,80,49.1,80,50,80s1.8,0,2.6-0.1l0.4-5.1c2.3-0.3,4.6-0.9,6.7-1.8l2.9,4.2 c1.6-0.7,3.1-1.6,4.5-2.6L65,69.9c1.9-1.4,3.5-3,4.9-4.9l4.6,2.2c1-1.4,1.9-2.9,2.6-4.5L73,59.8c0.9-2.1,1.5-4.4,1.8-6.7L79.9,52.6 z M50,65c-8.3,0-15-6.7-15-15c0-8.3,6.7-15,15-15s15,6.7,15,15C65,58.3,58.3,65,50,65z" fill="${this.gearColors.primary}">
                                                  <animateTransform attributeName="transform" type="rotate" from="90 50 50" to="0 50 50" dur="1s" repeatCount="indefinite">
                                                  </animateTransform>
                                              </path>
                                          </g>
                                          <g transform="translate(20,20) rotate(15 50 50)">
                                              <path d="M79.9,52.6C80,51.8,80,50.9,80,50s0-1.8-0.1-2.6l-5.1-0.4c-0.3-2.4-0.9-4.6-1.8-6.7l4.2-2.9c-0.7-1.6-1.6-3.1-2.6-4.5 L70,35c-1.4-1.9-3.1-3.5-4.9-4.9l2.2-4.6c-1.4-1-2.9-1.9-4.5-2.6L59.8,27c-2.1-0.9-4.4-1.5-6.7-1.8l-0.4-5.1C51.8,20,50.9,20,50,20 s-1.8,0-2.6,0.1l-0.4,5.1c-2.4,0.3-4.6,0.9-6.7,1.8l-2.9-4.1c-1.6,0.7-3.1,1.6-4.5,2.6l2.1,4.6c-1.9,1.4-3.5,3.1-5,4.9l-4.5-2.1 c-1,1.4-1.9,2.9-2.6,4.5l4.1,2.9c-0.9,2.1-1.5,4.4-1.8,6.8l-5,0.4C20,48.2,20,49.1,20,50s0,1.8,0.1,2.6l5,0.4 c0.3,2.4,0.9,4.7,1.8,6.8l-4.1,2.9c0.7,1.6,1.6,3.1,2.6,4.5l4.5-2.1c1.4,1.9,3.1,3.5,5,4.9l-2.1,4.6c1.4,1,2.9,1.9,4.5,2.6l2.9-4.1 c2.1,0.9,4.4,1.5,6.7,1.8l0.4,5.1C48.2,80,49.1,80,50,80s1.8,0,2.6-0.1l0.4-5.1c2.3-0.3,4.6-0.9,6.7-1.8l2.9,4.2 c1.6-0.7,3.1-1.6,4.5-2.6L65,69.9c1.9-1.4,3.5-3,4.9-4.9l4.6,2.2c1-1.4,1.9-2.9,2.6-4.5L73,59.8c0.9-2.1,1.5-4.4,1.8-6.7L79.9,52.6 z M50,65c-8.3,0-15-6.7-15-15c0-8.3,6.7-15,15-15s15,6.7,15,15C65,58.3,58.3,65,50,65z" fill="${this.gearColors.secondary}">
                                                  <animateTransform attributeName="transform" type="rotate" from="0 50 50" to="90 50 50" dur="1s" repeatCount="indefinite">
                                                  </animateTransform>
                                              </path>
                                          </g>
                                       </svg>`;
        this.loaderBGColor     = 'rgba(0,0,0,0.4)';
        this.loaderMessage     = message;
        this.loaderTxtColor    = '#979d8f';
        this.loaderTxtShadow   = '1px 1px 0px #7f8576';
        this.loaderFont        = 'Helvetica, Arial';
        this.loaderHeading     = heading;
        this.loaderElem        = '';
        this.escKeyInitialized = false;
    }

    create() {
        this.loaderElem = `<div class="coreLoader"
                                style="background-color: ${this.loaderBGColor};
                                       position: fixed;
                                       left: 0;
                                       top: 0;
                                       z-index: 1000000;
                                       width: 100%;
                                       height: 100%;
                                       transition: any 1s;
                                       pointer-events: none;
                                       opacity: 0;">
                                <div style="text-align: center; margin-top: 20%">
                                    ${this.loaderImage}
                                </div>
                                <div style="color: ${this.loaderTxtColor};
                                            text-shadow: ${this.loaderTxtShadow};
                                            font-size: 32px;
                                            font-weight: 400;
                                            margin: auto;
                                            text-align: center;
                                            font-family:'${this.loaderFont}'">
                                    ${this.loaderHeading}
                                </div>
                                <div style="color: ${this.loaderTxtColor};
                                            text-shadow: ${this.loaderTxtColor};
                                            font-size: 16px;
                                            font-weight: 400;
                                            margin: auto;
                                            text-align: center;
                                            font-family: '${this.loaderFont}'">
                                    ${this.loaderMessage}
                                </div>
                            </div>`;
    }

    show() {
        var self = this;
        if (self.drawn) {
            $('.coreLoader').css('opacity',.8);
        } else {
            $('body').append(this.loaderElem);
            self.drawn = true;
            $('.coreLoader').css('opacity',.8);
            if (!self.escKeyInitialized) {
                $(document).keyup(function (e) {
                    if (e.keyCode === 27) {
                        self.hide();
                        self.escKeyInitialized = true;
                        ajaxSuccess();
                    }
                });
            }
        }
        self.timeTillDestroy = 1000;
        this.alive = true;
    }

    hide() {
        $('.coreLoader').css('opacity',0);
        this.countAndDestroy();
    }

    countAndDestroy() {
        var self = this;
        setInterval(function() {
            if (parseFloat($('.coreLoader').css('opacity')) <= 0) {
                self.timeTillDestroy -= 100;
                if (self.timeTillDestroy <= 0) {
                    self.alive = false;
                    self.destroy();
                }
            }
        },100);
    }

    destroy() {
        core.ELEMENTS.loader.alive = false;
        core.ELEMENTS.loader = undefined;
        $('.coreLoader').remove();
    }

    init() {
        this.create();
        this.show();
    }
};
//----------core Ajax----------

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
//----------core Notify Object----------

'use strict';

core.notify = class {
    constructor(type,message) {
        // success, info, error
        this.notifyType        = type;
        this.notifyHeading     = 'Successful';
        this.notifyImage       = `<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAqlQTFRFAHMucLCKi7+gAnQwJohNdLON2Orff7mWfbiUjsGi+vz7BHUxeraSjcCiLYxTncmvB3c0stXAmMarI4ZLU6FyV6N1Yah+0+fbYql+h72diL6d3OziBnYzb7CJhbybv9zLCXg1crKLe7eTeLWQHYNGfLeUfriVFH4+n8qwIYVJ7fXwGIBCudnGUZ9wutnGd7SQ3ezjdrSPIoZKnMmu/f79VqJ0HIJFUqBxZaqBaKyDksOm8ff0a66GA3Uw8PfzJYdMAXQvu9rHC3k3DHo49fr3WaR32erggLmX5/Lr3+3l+Pv5y+LUl8aqOZJdlMSnt9fEX6d8EHw77/byQJZiCng2nsqvQ5hl+fz6Qpdk0ubaSZtqBXYymsisc7KMwNzLN5FbVaJ0/P391OfcJIdM8vj0p8+3tNbC6vPuRZln6fPtg7uZ2uvhba+H6/TvVKFzLoxU4u/n0ebZ7vbxMY5W9/v4SJtpib6eq9G6WqR4Z6yC1ejdXKZ5FX8/+/38EXw87PXvgrqZOpNeo8y0wt7NgbqY4e/mocuybK6HlsWpP5ZiNpFam8itdbOODns5PZRgGoFDO5Ne5vHrx+DRpc61ZquCF4BBCHc16PLss9XBKIlPS5xrWKN2lcWoUJ9wRJhmGYFCebWR4/Dow97Oaa2EDXo5T55vL41VttfD2+vhrtO9MI1VSpxr1ujdxN/Pk8SmzeTWjMChKopQH4RHHoNHOJJcRplnhLuaIIVI5fHqG4JEzOPVmcerE30+pM20K4tRZKqAPpVhM49Y4O7moMuxkMKkQZdjr9O9zuTX9vr4Tp5uTZ1tvdvJkcOlPJRfir+f5PDpFn9AD3s6xuDQNZBZMo5XrNG7Y6l/ps62En099Pn2J4hOsNS+8/j1yuLUuNjFaq2FLItSosyz////HJAQlgAAAON0Uk5T/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wDMXgGwAAAGW0lEQVR42qya50MUORTAQ1uQhaWDVKmiCEhVAQVBQEFERKVYQBEVUATEevbePfWsZz/b2c6zXO+9997v8pfc7rqTMpPJJLO+T5PkJb9NNuXlvQAoKkuzd3teimh5ZLO178tKig660S9cFYgoFbQNyfIOBirxeqUr5nTlk4H0jggfA3Ql/aW9NW5DfiqcBAxk4u2gSjcgBaGLgJCc2NloElJbnAeEZXNatxlI6BggJZN6NslCduwH0pK3VgrSPB6YkvBvxCGt3uw2Zt3vWtEQGRlZuHpQZ84d+lsUMo9Re0aWz8dRGVindFl22oKnGYp+YpAiTcWRV8pLWb/Q/1jKdY3ys6XGkM5p6rW2IZu3Bv7655CqQmq1EeTPMNUGtd5w19jiN4OuE3yHD2lULfGhsSIb4MoUL3rJdPAgJXQ/vjomupkvC6cqJkbpQ1ZRDOsQKCFrR1EjZtGF/EDqtT8PpaSR6syvnTqQcaTWWCgtPmT9aWzICFInF5qQULIFHxYkh9Q4B01JJjnLQrSQheTOvhialKkTiFb6NRDyD6mDpqWN9bcokAH2aMoL+c/+ooKcwUXXoFuShls6WUtBCP7b0E1ZgNtaTkIKruKCKe5CILH2LxOQOpxd7DYD7sCtRRKQdJRrk2/TEqXOicCUDAQ5gDPvSDNSALiiyvp0Lj4rEAR3ZJ00Y76jWrwqM5DuigMyXdU7GRnPNh/wWZnggmShnJ9lGZeUmvl0fj5q0vsxpBPvNyslGQF4DP6jSzaiggEnZDtKrzDPAGA2VZSA8g86IedR+jk5xmTa6KAs4aUou8wJ+U5JjpJj+KrNuqlk6bsou8YOuaveaQQlQWM7/s4+JYPskBdRaph7DGAly6eg7AY75DMlUVErwYhh2NqhlMZ7eBIDOFJJfC7BGM5geNIq8Up+XCPIiFMSo5nNHWVt/Z4MRoBKZzEqyQTZDOuC/Mlz59YLMaI1mzMqmgdmo+9qBsPDUaA2V4cwGIGaqr8lKmU9AE2u4BIdBgCTqcwXhBgQblMKq0Cu8nmfs9y+JjKLGQxf1kijo+sIOKt8ruZNITxi5xiMZOaUQZbcIJipfBbqjBVN2Ws8d6njzLlQQDtemByGQpktzoB+6F4LbNRprHMPUA6/fAkG3qRTwb9sSLW2taS2BIE1yITsp00kdAkUdHRECxwFI8Fh5TNCY+kISCBnfzuIji0wFnkS+H+8NAN7Nq4D1NYtHWuHIzHcnboK3VPAGnScaByJewwYyfzj4CN0xwXYRM2BUGrEPPmM5s2KYhpYgiqVQymKAYO46IaCV714u1y8aQYMwQcVgGN0Ny+HDNVhBIhY+y5H01EAm5TExoUQio5YtLEZsE/RfWA3JLayjTMuJdCYUUoYvwBmGk1JDzMM+AzSrrdDmtFZvIh/BxFcgy65htSjHLZwE5Fkyx6ZNfhYKpGlddFpcGPDoEiviofM3HXKG0h/vROCrdZgf2OKGAOGkfcRx3Wuhes5plelIOMGIO8jgJoIE3S74lqVAVC2I0kuiH+ciMPOMWLDBRnYDga7lHv8Tpy3VL/mOLBG1OjHjoE5yFmwEEfeujhVhS9Jo/GPjsW+lWScu8R9B04soDvigpRMxDEMf7chNgxpJZ1qL+P82+4yiME6T7sH33xCLkgIdxO7QzcNIZw4YJ47jGGMnRS5bFcThV+YZ9QQzTzU+IW3xBGRmdfNMk5dZHkoALOf1nJzDAsZSvuSFRCg7uZBZhjlJ9lhCzK00UJS0uQZH5D1X9OJn6w6QWptqDW/Puxi0YsExSZSodzpMoh7RyhGtn7g7KlZlGbDKVGEP90N1Q6oCgF2VFC6V9dsEgs1PKAZIbwQoL0vVlr9YV2BIeLmj4DL0IZlO0apA79buQHN7uJbattP819qA8y9TRqLcU69Tjjw8vazs9TKZa3QGMK4wtvlUfxbA5TXeJXlj/kXvLSKVYzziBn0v2llmvIV28LfSfLz9fX1OR4xM519pfiQ1R77+YLlgrnnC31sM0DvIUbQt/IIq96tRfdJSUmSLKMqB8pC7GNWNEECseCufkvcZz7vH7cKIrI+4bVj8GCpP7rPmOCdG8Vvxfjp1fcBNh6hbHmmYRNCj8iqgyJtiYw3V94R9fdE6gPRzbw3M79n3czBM2GpqXnefYfD/epO7xKt+78AAwCJ+OE65etoZQAAAABJRU5ErkJggg==" alt="Success Icon"/>`;
        this.notifyPosition    = 'top: 10px; right: 10px';
        this.notifyMessage     = message;
        this.notifyWidth       = '250';
        this.notifyBGColor     = '#f7f7f7 !important';
        this.notifyColor       = '#828282 !important';
        this.notifyBoxShadow   = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
        this.border            = '1px solid #88a910';
        this.notifyRadius      = '3px';
        this.notifyTimeOut     = '4000';
        this.notifyElem        = "";
    }

    create() {
        if (this.notifyType === 'success') {
            this.notifyHeading      = 'SUCCESS';
            this.notifyBGColor      = '#badc52';
            this.notifyColor        = '#828282 !important';
            this.notifyBoxShadow    = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
            this.border             = '1px solid #88a910';
            this.notifyImage        = '<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAqlQTFRFAHMucLCKi7+gAnQwJohNdLON2Orff7mWfbiUjsGi+vz7BHUxeraSjcCiLYxTncmvB3c0stXAmMarI4ZLU6FyV6N1Yah+0+fbYql+h72diL6d3OziBnYzb7CJhbybv9zLCXg1crKLe7eTeLWQHYNGfLeUfriVFH4+n8qwIYVJ7fXwGIBCudnGUZ9wutnGd7SQ3ezjdrSPIoZKnMmu/f79VqJ0HIJFUqBxZaqBaKyDksOm8ff0a66GA3Uw8PfzJYdMAXQvu9rHC3k3DHo49fr3WaR32erggLmX5/Lr3+3l+Pv5y+LUl8aqOZJdlMSnt9fEX6d8EHw77/byQJZiCng2nsqvQ5hl+fz6Qpdk0ubaSZtqBXYymsisc7KMwNzLN5FbVaJ0/P391OfcJIdM8vj0p8+3tNbC6vPuRZln6fPtg7uZ2uvhba+H6/TvVKFzLoxU4u/n0ebZ7vbxMY5W9/v4SJtpib6eq9G6WqR4Z6yC1ejdXKZ5FX8/+/38EXw87PXvgrqZOpNeo8y0wt7NgbqY4e/mocuybK6HlsWpP5ZiNpFam8itdbOODns5PZRgGoFDO5Ne5vHrx+DRpc61ZquCF4BBCHc16PLss9XBKIlPS5xrWKN2lcWoUJ9wRJhmGYFCebWR4/Dow97Oaa2EDXo5T55vL41VttfD2+vhrtO9MI1VSpxr1ujdxN/Pk8SmzeTWjMChKopQH4RHHoNHOJJcRplnhLuaIIVI5fHqG4JEzOPVmcerE30+pM20K4tRZKqAPpVhM49Y4O7moMuxkMKkQZdjr9O9zuTX9vr4Tp5uTZ1tvdvJkcOlPJRfir+f5PDpFn9AD3s6xuDQNZBZMo5XrNG7Y6l/ps62En099Pn2J4hOsNS+8/j1yuLUuNjFaq2FLItSosyz////HJAQlgAAAON0Uk5T/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wDMXgGwAAAGW0lEQVR42qya50MUORTAQ1uQhaWDVKmiCEhVAQVBQEFERKVYQBEVUATEevbePfWsZz/b2c6zXO+9997v8pfc7rqTMpPJJLO+T5PkJb9NNuXlvQAoKkuzd3teimh5ZLO178tKig660S9cFYgoFbQNyfIOBirxeqUr5nTlk4H0jggfA3Ql/aW9NW5DfiqcBAxk4u2gSjcgBaGLgJCc2NloElJbnAeEZXNatxlI6BggJZN6NslCduwH0pK3VgrSPB6YkvBvxCGt3uw2Zt3vWtEQGRlZuHpQZ84d+lsUMo9Re0aWz8dRGVindFl22oKnGYp+YpAiTcWRV8pLWb/Q/1jKdY3ys6XGkM5p6rW2IZu3Bv7655CqQmq1EeTPMNUGtd5w19jiN4OuE3yHD2lULfGhsSIb4MoUL3rJdPAgJXQ/vjomupkvC6cqJkbpQ1ZRDOsQKCFrR1EjZtGF/EDqtT8PpaSR6syvnTqQcaTWWCgtPmT9aWzICFInF5qQULIFHxYkh9Q4B01JJjnLQrSQheTOvhialKkTiFb6NRDyD6mDpqWN9bcokAH2aMoL+c/+ooKcwUXXoFuShls6WUtBCP7b0E1ZgNtaTkIKruKCKe5CILH2LxOQOpxd7DYD7sCtRRKQdJRrk2/TEqXOicCUDAQ5gDPvSDNSALiiyvp0Lj4rEAR3ZJ00Y76jWrwqM5DuigMyXdU7GRnPNh/wWZnggmShnJ9lGZeUmvl0fj5q0vsxpBPvNyslGQF4DP6jSzaiggEnZDtKrzDPAGA2VZSA8g86IedR+jk5xmTa6KAs4aUou8wJ+U5JjpJj+KrNuqlk6bsou8YOuaveaQQlQWM7/s4+JYPskBdRaph7DGAly6eg7AY75DMlUVErwYhh2NqhlMZ7eBIDOFJJfC7BGM5geNIq8Up+XCPIiFMSo5nNHWVt/Z4MRoBKZzEqyQTZDOuC/Mlz59YLMaI1mzMqmgdmo+9qBsPDUaA2V4cwGIGaqr8lKmU9AE2u4BIdBgCTqcwXhBgQblMKq0Cu8nmfs9y+JjKLGQxf1kijo+sIOKt8ruZNITxi5xiMZOaUQZbcIJipfBbqjBVN2Ws8d6njzLlQQDtemByGQpktzoB+6F4LbNRprHMPUA6/fAkG3qRTwb9sSLW2taS2BIE1yITsp00kdAkUdHRECxwFI8Fh5TNCY+kISCBnfzuIji0wFnkS+H+8NAN7Nq4D1NYtHWuHIzHcnboK3VPAGnScaByJewwYyfzj4CN0xwXYRM2BUGrEPPmM5s2KYhpYgiqVQymKAYO46IaCV714u1y8aQYMwQcVgGN0Ny+HDNVhBIhY+y5H01EAm5TExoUQio5YtLEZsE/RfWA3JLayjTMuJdCYUUoYvwBmGk1JDzMM+AzSrrdDmtFZvIh/BxFcgy65htSjHLZwE5Fkyx6ZNfhYKpGlddFpcGPDoEiviofM3HXKG0h/vROCrdZgf2OKGAOGkfcRx3Wuhes5plelIOMGIO8jgJoIE3S74lqVAVC2I0kuiH+ciMPOMWLDBRnYDga7lHv8Tpy3VL/mOLBG1OjHjoE5yFmwEEfeujhVhS9Jo/GPjsW+lWScu8R9B04soDvigpRMxDEMf7chNgxpJZ1qL+P82+4yiME6T7sH33xCLkgIdxO7QzcNIZw4YJ47jGGMnRS5bFcThV+YZ9QQzTzU+IW3xBGRmdfNMk5dZHkoALOf1nJzDAsZSvuSFRCg7uZBZhjlJ9lhCzK00UJS0uQZH5D1X9OJn6w6QWptqDW/Puxi0YsExSZSodzpMoh7RyhGtn7g7KlZlGbDKVGEP90N1Q6oCgF2VFC6V9dsEgs1PKAZIbwQoL0vVlr9YV2BIeLmj4DL0IZlO0apA79buQHN7uJbattP819qA8y9TRqLcU69Tjjw8vazs9TKZa3QGMK4wtvlUfxbA5TXeJXlj/kXvLSKVYzziBn0v2llmvIV28LfSfLz9fX1OR4xM519pfiQ1R77+YLlgrnnC31sM0DvIUbQt/IIq96tRfdJSUmSLKMqB8pC7GNWNEECseCufkvcZz7vH7cKIrI+4bVj8GCpP7rPmOCdG8Vvxfjp1fcBNh6hbHmmYRNCj8iqgyJtiYw3V94R9fdE6gPRzbw3M79n3czBM2GpqXnefYfD/epO7xKt+78AAwCJ+OE65etoZQAAAABJRU5ErkJggg==" alt="Success Icon"/>';
        } else if (this.notifyType === 'info') {
            this.notifyHeading      = 'INFORMATION';
            this.notifyBGColor      = '#f4db05';
            this.notifyColor        = '#828282 !important';
            this.notifyBoxShadow    = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
            this.border             = '1px solid #b39c00';
            this.notifyImage        = '<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRFtaEC////CJBjtgAAAAJ0Uk5T/wDltzBKAAABHElEQVR42uzaQQ7DMAhE0c/9L1112cRJDIZJlOI16O1sCwabPAzOdG8U8EAsCZMOq8KMQwZxxZBDnDNkEWcMicahQiJxyJBrjBWSjaFCMjFkyDf2CgXGTqHC2CqUGBsFp+Es3COeTpfiQixYTNBwlRN+AR31xF/Z+Q4pQgWCBOEHQYBQheBFWENiV+t8E9F/Ik4kcoG7eoj9RilFQs8X1cZXeRNC+WnEiTztWNFvs5FGGmmkkUb+DbF6xBpxIlaN2LsQq0XsbcijJhJriNUhJkasCjE5YjWIf2RLdJgqRiRjdM1C4LrNh9iNiGUi9y7ONCtAzTJTs5bVLJg1q3LR0l8TX9AEMUSREk04RhTzEQWWRNErVYgsIQ73EWAAzeUK/pjiNswAAAAASUVORK5CYII=" alt="Info Icon"/>';
        } else if (this.notifyType === 'error') {
            this.notifyHeading      = 'ERROR';
            this.notifyBGColor      = '#f05a5c';
            this.notifyColor        = '#828282 !important';
            this.notifyBoxShadow    = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
            this.border             = '1px solid red';
            this.notifyImage        = '<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABjCAMAAABaOVXeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAFdQTFRF0A8K65iW7J2a5YKA7qWk5Xp354WC6ZOR7qim7J+d5Hp354B965qY5XVz4nNx6piW6YqI6YyK6paT7KGf6IWD6o6M6IyK43Nw43d06pOR5nd06IKA////1Iv0HAAAAB10Uk5T/////////////////////////////////////wBZhudqAAACgUlEQVR42sya23LCMAxENxcSoNwKBQr4/7+zlKEMBVuRVqLTfWztOVFQJFkyklJVhwed1tq9UAHmKGkbA+kwoJEX0kKlygGpodYHCTEgvrViIDBrY4WAkgkyAal3PeQIXloIXNJB4FQ1DKng1tsQZI0AzWXIFjGSIBXwCgpMDL13nIoQo2uqfQzKTfboM85CCIa4Kwd5szilCvMMGdPZIg1GS2jdpU12zCPkyOYKDQX6qChSFpIfwxB5k92Ye8gGfsoyt6O7g/gLkrN2xeeCqfipRUqT2XG8QdzliPCz/EBavJDyfoVElFZlShmS4j7K9gJZZcM0V1rnTUHJxJ6LYjs15LL8M+qFtWdIXQwGFKXJlGFIQoSmKDkvSlJGYyg6SKPIrB8WSo1KesolE8WeQzE68VUwLkZUmHZKr2Ask5dCREA7hQmzxZVdIGRmdTEqYSyML4w8vdoo7EHctHpB5lfT8jmbxC0tNbpSsLgYX45MAz4sz3ccCNFTFJCJm+KrEXUulunITX35+6q1vKi3QBqN+Rk7nv92YEyRmg5LsSSiKNa6i6JkIX0UZVRoZy2EgttOqfP/neQhe/P58/ZwdeHoMA0zBeVDUEIcJdNgnBUhG5ZSPJjOzKYYKFOpWRBFuXUkdvZxWGOGZJ/rEGLKXZeoHwrfNGWwqbb1U3517sbMmHKY0ah6kE6KsmXbelzsqS/cxzQGZEjST4/McwrVQGBEUeyjjWzJN1aWb/ohzeM09CCv/izOtDTf135vbjmATxTk4CyOMjAxfQEjIcVT/mLA/H9G5T6M4Y5EJEK47RGHEO+thDESogpF8HeJgqYqKUXVJM77XR6AGkLWyzd9CTAAHl7IPC2SjfkAAAAASUVORK5CYII=" alt="Error Icon"/>';
        }
        this.notifyElem = `<div class="coreNotification"
                                style="
                                       background-color: ${this.notifyBGColor};
                                       position: fixed;
                                       box-sizing: border-box;
                                       ${this.notifyPosition};
                                       overflow: hidden;
                                       border-radius: ${this.notifyRadius};
                                       border: ${this.border};
                                       box-shadow: ${this.notifyBoxShadow};
                                       display: none;
                                       z-index: 999999">
                                <div style="width: 60px;
                                            height: 100%;
                                            float: left;
                                            padding: 5px">
                                    ${this.notifyImage}
                                </div>
                                <div style="width: 200px;
                                            float: left;
                                            padding-left: 5px;
                                            padding-bottom: 5px;
                                            background-color: #f7f7f7;">
                                    <div style="color: ${this.notifyColor};
                                                font-weight: bold;">
                                        ${this.notifyHeading}
                                    </div>
                                    <div style="color: ${this.notifyColor};
                                                line-height: 15px;
                                                min-height: 35px">
                                        ${this.notifyMessage}
                                    </div>
                                </div>
                            </div>`;
    }

    show() {
        $('body').append(this.notifyElem);
        $(".coreNotification").fadeIn('slow');
    }

    hide() {
        var self = this;
        setTimeout(function() {
            $(".coreNotification").fadeOut('slow',function() {
                self.destroy();
            });
        },this.notifyTimeOut);
    }

    destroy() {
        $(".coreNotification").remove();
    }

    init(type,message) {
        this.notifyType     = type;
        this.notifyMessage  = message;
        this.create();
        this.show();
        this.hide();
    }
};

//----------core Confirmation Object----------

'use strict';

core.confirm = class {
    constructor(options) {
        Object.assign(this,options);
        this.boxShadow        = 'rgba(0, 0, 0, 0.380392) 0px 0px 14px 0px';
        this.bgColor          = 'white';
        this.url              = function() {};
        this.confirmCallback  = 'none';
        this.callbackContext  = 'window';
        this.message          = '';
        this.txtColor         = '#333';
        this.font             = 'Helvetica, Arial';
        this.heading          = '';
        this.elem             = '';

        this.confirmBtnLabel  = 'Confirm';
        this.confirmBtnClass  = 'btn btn-primary uk-button uk-button-primary';
        this.confirmBtnOffset = 'left';

        this.cancelBtnLabel   = 'Cancel';
        this.cancelBtnClass   = 'btn btn-default uk-button uk-button-default';
        this.cancelBtnOffset  = 'right';
    }

    create() {
        var self = this;
        switch(self.confirmBtnOffset) {
            case 'left': self.confirmBtnOffset = 'pull-left'; break;
            case 'center': self.confirmBtnOffset = 'text-center'; break;
            case 'right': self.confirmBtnOffset = 'pull-right'; break;
        }

        switch(self.cancelBtnOffset) {
            case 'left': self.cancelBtnOffset = 'pull-left'; break;
            case 'center': self.cancelBtnOffset = 'text-center'; break;
            case 'right': self.cancelBtnOffset = 'pull-right'; break;
        }

        this.elem = `<div
                        id="coreConfirm"
                        style="background-color: ${this.bgColor};
                               position: fixed;
                               left: calc(50% - 175px);
                               top: calc(50% - 100px);
                               z-index: 1000000;
                               width: 350px;
                               overflow: hidden;
                               border-radius: 5px;
                               box-shadow: ${this.boxShadow};
                               display: none;">
                        <div style="color: ${this.txtColor};
                                    font-size: 20px;
                                    font-weight: 400;
                                    margin: auto;
                                    text-align: center">
                            ${this.heading}
                        </div>
                        <div style="color: ${this.txtColor};
                                    font-size: 16px;
                                    font-weight: 400;
                                    margin: auto;
                                    text-align: center;
                                    padding: 10px">
                            ${this.message}
                        </div>
                        <div style="padding:10px; height:55px">
                            <div class="${this.confirmBtnOffset}">
                                <button id="coreConfirmConfirmButton" class="${this.confirmBtnClass}">${this.confirmBtnLabel}</button>
                            </div>
                            <div class="${this.cancelBtnOffset}">
                                <button id="coreConfirmCancelButton" class="${this.cancelBtnClass}">${this.cancelBtnLabel}</button>
                            </div>
                        </div>
                    </div>`;
    }

    show() {
        var self = this;
        $('body').append(this.elem);
        $('#coreConfirm').fadeIn('fast',function() {
            $("#coreConfirmConfirmButton").click(function() {
                if (self.url !== 'none') {
                    $.ajax({
                        url : self.url
                    });
                }
                if (self.confirmCallback !== 'none') {
                    // Gets the actual function object from the dot notation string inside arg['callback']
                    let callback = self.confirmCallback.split('.').reduce((o,i) => o[i],window);
                    // Gets the actual context object from the dot notation string inside arg['context']
                    let context = self.callbackContext.split('.').reduce((o,i) => o[i],window);
                    callback.call(context);
                }
                self.hide();
            });

            $("#coreConfirmCancelButton").click(function() {
                self.hide();
            });
        });
    }

    hide() {
        var self = this;
        $('#coreConfirm').fadeOut('fast',function() {
            self.destroy();
        });
    }

    destroy() {
        $('#coreConfirm').remove();
    }

    init(heading, message, url = 'none', confirmCallback = 'none', callbackContext = 'window') {
        this.heading         = heading;
        this.message         = message;
        this.url             = url;
        this.confirmCallback = confirmCallback;
        this.callbackContext = callbackContext;
        this.create();
        this.show();
    }
};

//----------core Offcanvas Object----------

'use strict';

core.offcanvas = class {
    constructor() {
        this.uniqueId       = '';

        this.useBackdrop    = true;
        this.backdropColor  = 'rgba(12, 12, 12, 0.329412)';
        this.backdrop       = '';

        this.width          = '240px';
        this.position       = 'right';
        this.bgColor        = 'white';
        this.color          = "#313534";
        this.boxShadow      = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
        this.font           = 'Roboto, sans-serif, Helvetica, Arial, sans-serif';
        this.fontSize       = '13px';

        this.heading        = 'heading';
        this.body           = 'body';

        this.canvas         = '';
    }

    guid() {
        function s4() {
          return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
          s4() + '-' + s4() + s4() + s4();
    }

    create() {
        this.canvas = `<div id="${this.uniqueId}"
                            class="coreOffcanvas"
                            style="width: ${this.width}; ${this.position}: 0px; background-color: ${this.bgColor};
                                   color: ${this.color}; position: fixed; top: 0; box-shadow: ${this.boxShadow};
                                   font-family: ${this.font}; font-size: ${this.fontSize}; height: 100%; z-index: 999998;
                                   overflow-y: auto; overflow-x: hidden">
                            <div
                                style="box-sizing: border-box; display: block; height: 44px; line-height: 24px;
                                       padding: 4px 16px;
                                       position: relative; text-size-adjust: 100%; width: ${this.width};">
                                <header style="font-size: 20px; height: 36px; line-height: 37px; text-size-adjust: 100%;">
                                    ${this.heading}
                                </header>
                                <div class="offcanvasCloseButton"
                                     style="display: block; height: 36px; line-height: 36px; position: absolute; right: 16px;
                                            text-align: right; text-size-adjust: 100%; top: 4px; cursor: pointer;
                                            font-size: 20px; width: calc(${this.width} - 8px);"
                                     id="close_${this.uniqueId}">
                                    &times;
                                </div>
                            </div>
                            <div style="padding: 16px;">
                                ${this.body}
                            </div>
                        </div>`;
    }

    createBackdrop() {
        this.backdrop = `<div id="backdrop_${this.uniqueId}"
                              style="background-color: ${this.backdropColor}; position: fixed; height: 100%; width: 100%;
                                     top: 0; left: 0; z-index: 999980; display: none"></div>`;
    }

    activateClose() {
        var self = this;
        $('#close_'+self.uniqueId+',#backdrop_'+self.uniqueId).click(function() {
            self.hide();
        });
    }

    show() {
        let body = $('body');
        body.append(this.canvas);
        $("#"+this.uniqueId).show();
        body.css('overflow','hidden');
    }

    showBackdrop() {
        $('body').append(this.backdrop);
        $('#backdrop_'+this.uniqueId).fadeIn(800);
    }

    hideBackdrop() {
        var self = this;
        $('#backdrop_'+this.uniqueId).fadeOut(800,function() {
            self.destroyBackdrop();
        });
    }

    destroyBackdrop() {
        $('#backdrop_'+this.uniqueId).remove();
    }

    hide() {
        var self = this;
        $("#"+self.uniqueId).hide();
        $('body').css('overflow','auto');
        self.destroy();
        self.hideBackdrop();
    }

    destroy() {
        $('#'+this.uniqueId).remove();
    }

    init(heading,body,width) {
        this.heading = heading;
        this.body = body;
        if (typeof width !== 'undefined') {
            this.width = width;
        } else {
            this.width = '240px';
        }

        this.uniqueId = 'offcanvas_'+this.guid();
        this.create();

        if (this.useBackdrop) {
            this.createBackdrop();
            this.showBackdrop();
        }
        this.show();
        this.activateClose();
    }
};
//----------Heepp App Object----------

'use strict';

core.app = class {
    constructor() {

    }

    numberFormat(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number;
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
        var s = '';

        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };

        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }

    round(value, precision, mode) {
        var m, f, isHalf, sgn; // helper variables
        // making sure precision is integer
        precision |= 0;
        m = Math.pow(10, precision);
        value *= m;
        // sign of the number
        sgn = (value > 0) | -(value < 0);
        isHalf = value % 1 === 0.5 * sgn;
        f = Math.floor(value);
        if (isHalf) {
            switch (mode) {
                case 'PHP_ROUND_HALF_DOWN':
                    // rounds .5 toward zero
                    value = f + (sgn < 0);
                break;
                case 'PHP_ROUND_HALF_EVEN':
                    // rounds .5 towards the next even integer
                    value = f + (f % 2 * sgn);
                break;
                case 'PHP_ROUND_HALF_ODD':
                    // rounds .5 towards the next odd integer
                    value = f + !(f % 2);
                break;
                default:
                    // rounds .5 away from zero
                    value = f + (sgn > 0);
            }
        }
        return (isHalf ? value : Math.round(value)) / m
    }
};
//----------Heepp Form Object----------

'use strict';

core.form = class {
    constructor(formId) {
        this.form       = $("#"+formId);
        this.formAction = './';
        this.formMethod = 'post';
        this.formAsync  = true;
        this.formData   = {};
        this.formFields = [];
        this.formErrors = {};
    }

    init() {
        this.formAction = this.form.attr('action');
        this.formMethod = this.form.attr('method');
        this.formFields = $(this.form).find(':input');
        this.formAsync  = this.form.attr('async');
        var self        = this;

        if (typeof this.formAsync === 'undefined') {
            this.formAsync = true;
        }

        this.form.submit(function(event) {
            event.preventDefault();
            self.formFields = self.form.serialize();
            $.ajax({
                url     : self.formAction,
                method  : self.formMethod,
                async   : self.formAsync,
                data    : self.formFields
            });
        });
    }
};

//----------Heepp HTML Object----------

'use strict';

core.html = class {
    constructor() {
        this.htmlFunc   = null;
        this.htmlTarget = null;
        this.html       = null;
    }
    
    replace() {
        $(this.htmlTarget).html(this.html); 
    }
    
    append() {
        $(this.htmlTarget).append(this.html);
    }
    
    prepend() {
        $(this.htmlTarget).prepend(this.html);
    }
    
    remove() {
        $(this.htmlTarget).empty();
    }
    
    init(func,target,html) {
        this.htmlFunc   = func;
        this.htmlTarget = target;
        this.html       = html;
        self = this;
        
        switch(this.htmlFunc) {
            case 'replace':
                self.replace();
            break;
            case 'append':
                self.append();
            break;
            case 'prepend':
                self.prepend();
            break;
            case 'remove':
                self.remove();
            break;
        }
    }
};
//----------Heepp Value Object----------

'use strict';

core.value = class {
    constructor() {
        this.target = null;
        this.value       = null;
    }
    
    setValue() {
        $(this.target).val(this.value);
    }
    
    init(target,value) {
        this.target = target;
        this.value  = value;
        this.setValue();
    }
};
//----------Heepp Event Object----------

'use strict';

core.event = {};
//----------Heepp Click Event Object----------

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

//----------Heepp Document Ready Object----------

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

//----------Heepp Date Object----------

'use strict';

// Important: This class must be a singleton
let coreDateInstance = null;

core.date = class {
    constructor() {
        // This checks if the instance has been instantiated
        if(!coreDateInstance) {
            // If there is no instance, create one with this.
            coreDateInstance = this;
        }
        
        // Clock Element, html element where a clock can be displayed
        this.domElement = undefined;
        
        // date properties
        this.now = new Date();
        this.year = this.now.getFullYear();
        this.month = (this.now.getMonth()+1);
        this.day = this.now.getDate();
        this.hour = this.now.getHours();
        this.minute = this.now.getMinutes();
        this.second = this.now.getSeconds();
        this.dateTime = this.date+' '+this.time;
        this.date = null;
        this.time = null;

        // Return the instance
        return coreDateInstance;
    }
    
    timer() {
        this.now = new Date();
        this.year = this.now.getFullYear();
        this.month = (this.now.getMonth()+1);
        this.day = this.now.getDate();
        this.hour = this.now.getHours();
        this.minute = this.now.getMinutes();
        this.second = this.now.getSeconds();
        
        this.setDate();
        this.setTime();
        this.setDateTime();
        this.clock();
    }
    
    setDate() {
        this.date = this.now.getFullYear()+'-'+(this.now.getMonth()+1)+'-'+this.now.getDate();
    }
    
    setTime() {
        this.time = this.now.getHours() + ":" + this.now.getMinutes() + ":" + this.now.getSeconds();
    }
    
    setDateTime() {
        this.dateTime = this.date+' '+this.time;
    }
   
    clock(clockSelector) {
        // If the domElement is not set then the previous one will be used
        this.clockSelector = clockSelector || this.clockSelector;
        $(clockSelector).html(this.time);
    }
    
    init() {
        var self = this;
        setInterval(function() { 
            self.timer();
        }, 1000);
    }
};
let coreDate = new core.date();
coreDate.init();

//----------Heepp GET Request Object----------

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

//----------Heepp API Object----------

'use strict';

core.api = class {
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

