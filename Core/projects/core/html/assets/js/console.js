'use strict';

core.console = class {
    constructor() {

    }
};
// Important: This class must be a singleton
let consoleInstance = null;

// main application singleton
class Console {
    constructor() {
        this.windowHeight = function() {
            return $(window).height();
        };
        this.windowWidth = function() {
            return $(window).width();
        };
        // This checks if the instance has been instantiated
        if(!consoleInstance) {
            // If there is no instance, create one for us.
            consoleInstance = this;
        }

        // Property for each part of the app
        this.sessionExists = false;
        this.console       = {};
        this.settings      = {};
        this.projects      = {};
        this.shortcuts     = {};
        this.login         = {};
        this.dashboard     = {};
        this.data          = {};
        this.common        = {};
        this.nav           = {};
        this.ui            = {};

        // Return the instance
        return consoleInstance;
    }
}
core.console = new Console();
