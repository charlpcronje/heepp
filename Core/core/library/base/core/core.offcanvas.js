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