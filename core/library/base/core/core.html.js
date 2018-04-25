'use strict';

core.html = class {
    constructor() {
        this.htmlFunc   = null;
        this.htmlTarget = null;
        this.html       = null;
    }
    
    replace() {
        $(this.htmlTarget).html(this.html); 
    }
    
    append() {
        $(this.htmlTarget).append(this.html);
    }
    
    prepend() {
        $(this.htmlTarget).prepend(this.html);
    }
    
    remove() {
        $(this.htmlTarget).empty();
    }
    
    init(func,target,html) {
        this.htmlFunc   = func;
        this.htmlTarget = target;
        this.html       = html;
        self = this;
        
        switch(this.htmlFunc) {
            case 'replace':
                self.replace();
            break;
            case 'append':
                self.append();
            break;
            case 'prepend':
                self.prepend();
            break;
            case 'remove':
                self.remove();
            break;
        }
    }
};