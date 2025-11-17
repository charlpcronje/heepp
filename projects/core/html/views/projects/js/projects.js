'use strict';

class projects {
    constructor() {
        this.project = {};
        this.ws      = $('#cc-workspace');
        this.wsLeft  = this.ws.find('section.ws-left');
        this.wsRight = this.ws.find('section.ws-right');
    }

    getCurrentProject() {
        return 'test';
    }

    init() {
        core.console.ui.toggleRightWS('hidden','hidden');
    }
}
