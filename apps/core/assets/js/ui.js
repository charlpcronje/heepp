'use strict';

class ui {
    constructor() {
        this.workspace       = {};
        this.ws              = $('#cc-workspace');
        this.wsLeft          = this.ws.find('section.ws-left');
        this.wsRight         = this.ws.find('section.ws-right');
        this.showRightWSIcon = $('#show-right-ws');
        this.hideRightWSIcon = $('#hide-right-ws');
        this.wsRightState    = 'visible';
        this.wsWidth = ()=>{
            return this.ws.width();
        };
        this.wsLeftWidth  = ()=>{
            return this.wsLeft.width();
        };
        this.wsRightWidth = ()=>{
            return this.wsRight.width();
        };
    }

    showRestoreSessionButton() {
        $('.restore-ui-session-button').show();
        setTimeout(function() {
            $('#top-right-restore-ui-session-button').hide();
        },10000)
    }

    checkIfSessionExists() {
        core.get.route('Console/sessionHistoryExist');
    }

    toggleRightWS(toggleClass = 'hidden',setState = undefined) {
        if (setState != undefined) {
            this.wsRight.removeClass(toggleClass);
            this.wsRight.addClass(setState);
        } else {
            /* CSS 'hidden' class will hide ws-right */
            this.wsRight.toggleClass(toggleClass);
        }

        /* Checking the state of ws-right and setting class property */
        if(this.wsRight.hasClass('hidden')) {
            this.wsRightState = 'hidden';

            /* Adding icon to top-right nav, when clicked the
             * ws-right will be back */
            this.showRightWSIcon.show();
            this.hideRightWSIcon.hide();
        } else {
            this.wsRightState = 'visible';
            this.showRightWSIcon.hide();
            this.hideRightWSIcon.show();
        }
        $('.nice-scroll').getNiceScroll().resize();
    }


    hideRightWS() {
        this.wsRight.hide();
        this.showRightWSIcon.show();
    }

    showRightWS() {
        this.wsRight.show();
        this.showRightWSIcon.hide();
    }

    initHideRightWSIcon() {
        this.hideRightWSIcon.click(()=>{
            this.toggleRightWS('hidden','hidden');
        });

        this.showRightWSIcon.click(()=>{
            this.toggleRightWS('hidden','visible');
        });
    }

    init() {
        this.checkIfSessionExists();
        this.initHideRightWSIcon();
    }
}

$(()=>{
    core.console.ui = new ui();
});

