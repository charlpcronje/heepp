/*global $,document */
/*jshint -W043 */
/*jslint browser: true*/
(function () {
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
            else if (is.length==1 && value!==undefined) {
                obj[is[0]] = value;
                return obj[is[0]];
            } else if (is.length==0) {
                return obj;
            } else {
                return this.strToDot(obj[is[0]],is.slice(1), value);
            }
        },
        select : {
            id       : document.getElementById.bind(document),
            class    : document.getElementsByClassName.bind(document),
            tag      : document.getElementsByTagName.bind(document),
            query    : document.querySelector.bind(document),
            queryAll : document.querySelectorAll.bind(document),
            tagNS    : document.getElementsByTagNameNS.bind(document)
        },
        sel : this.select
    };

    $(document).ready(function() {
        core.ajaxSuccess();
    });
})();
