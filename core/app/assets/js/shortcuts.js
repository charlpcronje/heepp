'use strict';

class shortcuts {
    constructor() {
        this.fgx = false;
    }

    init() {
        var self = this;
        Mousetrap.bind('shift+f shift+g shift+x', function(e) {
            self.fgx = true;
            $.ajax({
                url : UI_CONSTANTS.PROJECT.BASE_PATH+'core@console'
            });
        });

        Mousetrap.bind('`',function(e) {
            if (self.fgx) {
                $.ajax({
                    url : UI_CONSTANTS.PROJECT.BASE_PATH + 'core@console'
                });
            }
        });
    }
}

core.console.shortcuts = new shortcuts();
core.console.shortcuts.init();