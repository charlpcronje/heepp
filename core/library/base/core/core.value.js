'use strict';

core.value = class {
    constructor() {
        this.target = null;
        this.value       = null;
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