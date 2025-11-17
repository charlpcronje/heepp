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
