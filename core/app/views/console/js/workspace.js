'use strict';

ui.workspace = class {
    constructor() {
        this.ccWorkspace = $("#cc-workspace");
        this.topWSTabs = ()=> {
            return $('.cc-ws-tab-top');
        };
        this.topWSTabCloseIcons = ()=> {
            return $('.cc-ws-tab-top').find('.cc-ws-tab-close');
        };
        this.activeOpacity = '1.0';
        this.fadedOpacity  = '0.1';
    };

    activateClickedTabAndFadeOther() {
        this.topWSTabs().each((i,tab)=>{
            if ($(tab).hasClass('tab-clicked')) {
                $(tab).removeClass('tab-clicked');
                $(tab).addClass('active');
                $($(tab).attr('target')).children().css('opacity',this.activeOpacity);
            } else {
                $(tab).removeClass('active');
                $($(tab).attr('target')).children().css('opacity',this.fadedOpacity);
            }
        });
        $('.cc-ws-tab').css('opacity',1.0);
    }

    initWorkspaceTopTabs() {
        this.ccWorkspace.on('click','.cc-ws-tab-top',(event)=>{
            if (!$(event.currentTarget).hasClass('active')) {
                $(event.currentTarget).addClass('tab-clicked');
                this.activateClickedTabAndFadeOther();
            }
        });
        this.ccWorkspace.on('click','.cc-ws-tab-close',(event)=>{
            let target = $(event.currentTarget).attr('target');
            $(target).children().remove();
            let tab = $(event.currentTarget).parent('.cc-ws-tab');
            $(tab).remove();
        });
    }

    init() {
        this.initWorkspaceTopTabs();
    }
};
