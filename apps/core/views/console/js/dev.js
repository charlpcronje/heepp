'use strict';

class dev {
    constructor() {
        this.parentElement = $('html');
        this.acidDomLoaded = false;
        this.infoBlock = $('#dev-info-block');
    }

    loadAcidDom() {
        const self = this;
        $('#dev-info-block').find('.dev-info-inner-block').removeClass('open');
        if (!self.acidDomLoaded) {
            $.ajax({
                beforeSend : function() {
                    $('#dev-info-block').find('#dev-info-block-elements').addClass('open');
                },
                dataType : 'script',
                url : UI_CONSTANTS.PROJECT.PROJECT_ASSETS_PATH+'vendor/acidDom/js/acid_dom.js',
                complete : function() {
                    // $('.dev-info-inner-block:visible').height($('#dev-info-block').height());
                    core.console.dev.checkAcidDomReady();
                }
            });
        } else {
            window.ADI.toggle();
        }
    }

    checkAcidDomReady(elem) {
        setTimeout(function() {
            if ($("#adi-wrapper").length > 0) {
                // window.ADI.toggle();
            } else {
                core.console.dev.checkAcidDomReady();
            }
        },300);
    }

    loadConsole() {
        const self = this;
        self.infoBlock.find('.dev-info-inner-block').removeClass('open');
        self.infoBlock.find('#dev-info-block-console').addClass('open');
    }

    loadSources() {
        const self = this;
        self.infoBlock.find('.dev-info-inner-block').removeClass('open');
        self.infoBlock.find('#dev-info-block-sources').addClass('open');
    }

    loadNetwork() {
        const self = this;
        self.infoBlock.find('.dev-info-inner-block').removeClass('open');
        self.infoBlock.find('#dev-info-block-network').addClass('open');
    }

    loadData() {
        const self = this;
        self.infoBlock.find('.dev-info-inner-block').removeClass('open');
        self.infoBlock.find('#dev-info-block-data').addClass('open');
    }

    applyIconCommands() {
        const self = this;
        $('#info-panel-icon-nav').find('.icon-nav-action').click(function(elem) {
            var action = $(this).attr('action');
            var heading = $(this).find('span').html();
            $('#info-panel-icon-nav-heading').text(heading);
            eval('core.console.dev.'+action+'()');
        });

    }

    init() {
        const self = this;
        // Icon Click Bindings
        this.applyIconCommands();
    }
}
