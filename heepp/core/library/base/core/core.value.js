'use strict';

core.value = class {
    target = null;
    value = null;

    constructor({target,value} = options) {
        Object.assign(this,options);
    }
    
    setValue() {
        $(this.target).val(this.value);
    }
    
    init(target,value) {
        this.target = target;
        this.value  = value;
        this.setValue();
    }
};