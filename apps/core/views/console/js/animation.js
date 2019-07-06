"use strict";

class animation {
    constructor() {

    }

    svgToBottom() {
        const self = this;
        $('.ws-svg-box').css('top',window.core.console.ui.ws.height + 'px');
    }

    animSvg() {
        const self = this;
        /* Fade in svg */
        // noinspection JSUnresolvedFunction
        var ie = (!!navigator.userAgent.match(/Trident\/7\./)) ? 0.5 : 1,perspective = 1200;
        const tl = new TimelineLite();
        tl.to('.ws-svg-box',0.1,{top : window.core.console.ui.ws.height + 'px'})
          .to('.ws-svg-box',1,{opacity : 1})
          .to('#core-svg .svg-path',1,{fill : '#222'},'makeSVGvisible')
          .to('#core-svg .svg-path.top',0.5,{fill: '#444'},'makeSVGtopBrighter')
          .to('.ws-svg-box',1,{top : core.console.ui.ws.credits.top},'coreTOcore')
          .to('#core-svg .svg-path.top',0.5,{fill: '#222'},'makeSVGtopDimmer')
          .to('#core-svg .svg-path.right',0.5,{fill: '#444'},'makeSVGtopDimmer')
          .to('#core-svg',0.5,{rotation:45},'makeSVGtopDimmer+=0.35')
          .to('#core-svg .svg-path.right',0.5,{fill: '#222'},'makeSVGtopDimmer+=0.55')
          .to('#core-svg .svg-path.top',0.5,{fill: '#444'},'makeSVGtopDimmer+=0.85')
          .to('.ws-svg-box',1.7,{top : 90,left: 270},'coreABVcore')
          .to('#core-svg',0.5,{rotation:0},'coreABVcore+=0.5')
          .to('#core-svg',0.5,{rotation:0},'coreABVcore+=0.5');
        tl.seek('coreABVcore');
        // .to('.ws-svg-box.right',0.2,{fill    : '#FFF'});

        // TweenMax.to('.ws-svg-box',3,{
        //     delay : 1,
        //     // transform: 0,
        //     fill: '#161616',
        //     top: '170px',
        //     // rotation:360,
        //     // ease:Elastic.easeOut
        //     // ease:Bounce.easeOut
        //     // ease:Back.easeOut
        // });
    }

    testTweenMax() {
        //this.getElementHeight();
        // this.svgToBottom();
        this.animSvg();
        this.makeUIDraggable();

        // TweenMax.from('#core-svg',2,{
        // transform: 0,
        // fill: '#FFF',
        // x: 125,
        // rotation:360,
        // ease:Back.easeOut
        // ease:Elastic.easeOut
        // ease:Bounce.easeOut

        // });

        /*
         TweenMax.to('.core-svg-top',2,{
         width: 100,
         height: 100,
         x : '30px',
         y: '0px'
         });
         TweenMax.to('.core-svg-left',2,{
         x : '0px',
         y: '20px',
         });
         TweenMax.to('.core-svg-right',2,{
         x : '60px',
         y: '20px',
         });
         TweenMax.to('.svg-path:nth-child(1)',1,{
         fill: '#FFF',
         delay: 2.5
         });
         TweenMax.to('.svg-path:nth-child(2)',2,{
         fill: '#555',
         });
         TweenMax.to('.svg-path:nth-child(3)',3,{
         fill: '#555'
         });
         */
    }


    init() {

    }
}


