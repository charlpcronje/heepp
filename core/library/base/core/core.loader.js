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