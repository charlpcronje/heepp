'use strict';

// Important: This class must be a singleton
let coreDateInstance = null;

core.date = class {
    constructor() {
        // This checks if the instance has been instantiated
        if(!coreDateInstance) {
            // If there is no instance, create one with this.
            coreDateInstance = this;
        }
        
        // Clock Element, html element where a clock can be displayed
        this.domElement = undefined;
        
        // date properties
        this.now = new Date();
        this.year = this.now.getFullYear();
        this.month = (this.now.getMonth()+1);
        this.day = this.now.getDate();
        this.hour = this.now.getHours();
        this.minute = this.now.getMinutes();
        this.second = this.now.getSeconds();
        this.dateTime = this.date+' '+this.time;
        this.date = null;
        this.time = null;

        // Return the instance
        return coreDateInstance;
    }
    
    timer() {
        this.now = new Date();
        this.year = this.now.getFullYear();
        this.month = (this.now.getMonth()+1);
        this.day = this.now.getDate();
        this.hour = this.now.getHours();
        this.minute = this.now.getMinutes();
        this.second = this.now.getSeconds();
        
        this.setDate();
        this.setTime();
        this.setDateTime();
        this.clock();
    }
    
    setDate() {
        this.date = this.now.getFullYear()+'-'+(this.now.getMonth()+1)+'-'+this.now.getDate();
    }
    
    setTime() {
        this.time = this.now.getHours() + ":" + this.now.getMinutes() + ":" + this.now.getSeconds();
    }
    
    setDateTime() {
        this.dateTime = this.date+' '+this.time;
    }
   
    clock(clockSelector) {
        // If the domElement is not set then the previous one will be used
        this.clockSelector = clockSelector || this.clockSelector;
        $(clockSelector).html(this.time);
    }
    
    init() {
        var self = this;
        setInterval(function() { 
            self.timer();
        }, 1000);
    }
};
let coreDate = new core.date();
coreDate.init();
