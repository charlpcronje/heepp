'use strict';

class ui {
    constructor() {
        this.workspace = {};

        this.elem = {
            win : $(window),
            // cc "console container"
            cc  : $('#cc'),
            // ws "workspace"
            ws  : $('#cc-workspace')
        };
        this.win = {
            height : window.core.console.windowHeight,
            width  : window.core.console.windowWidth
        };
        this.cc = {
            height : this.elem.cc.innerHeight(),
            width  : this.elem.cc.innerWidth()
        };
        this.ws = {
            height : this.elem.ws.height(),
            width  : this.elem.ws.innerWidth()
        };
        this.ws.credits = {
            height : this.elem.ws.find('.ws-left-credit').height(),
            width  : this.elem.ws.find('.ws-left-credit').width(),
            top    : parseInt(this.elem.ws.find('.ws-left-credit').css('margin-top'))+$('.ws-left-credit').height()+35,
            left   : parseInt(this.elem.ws.find('.ws-left-credit').css('margin-left'))
        };
    }

    resize() {
        // Resize UI when window resize
        $(window).resize(function () {

        });
        $(window).resize();
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

    init() {
        const ui = this;
        $(() => {
            ui.resize(ui);
            this.checkIfSessionExists();
        });
    }
}

core.console.ui = new ui();
