'use strict';

class ui {
    constructor() {
        this.workspace.ws= {};
    }

    checkIfSessionExists() {
        core.get.route('Blog/sessionHistoryExist');
    }

    init() {
        this.checkIfSessionExists();
    }
}

$(()=>{
    core.console.ui = new ui();
});

