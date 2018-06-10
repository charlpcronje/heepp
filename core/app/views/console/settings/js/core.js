'use strict';

settings.core = class {
    constructor() {
        this.settingsForm = $('#core-settings-form');
    }

    submitToggles() {
        this.settingsForm.find('input').change(()=> {
            this.settingsForm.submit();
        });
    }

    init() {
        $(()=>{
            this.submitToggles();
        });
    }
};

