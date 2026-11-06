'use strict';

core.form = class {
    constructor(formId) {
        this.form       = $("#"+formId);
        this.formAction = './';
        this.formMethod = 'post';
        this.formAsync  = true;
        this.formData   = {};
        this.formFields = [];
        this.formErrors = {};
    }

    init() {
        this.formAction = this.form.attr('action');
        this.formMethod = this.form.attr('method');
        this.formFields = $(this.form).find(':input');
        this.formAsync  = this.form.attr('async');
        var self        = this;

        if (typeof this.formAsync === 'undefined') {
            this.formAsync = true;
        }

        this.form.submit(function(event) {
            event.preventDefault();
            self.formFields = self.form.serialize();
            $.ajax({
                url     : self.formAction,
                method  : self.formMethod,
                async   : self.formAsync,
                data    : self.formFields
            });
        });
    }
};
