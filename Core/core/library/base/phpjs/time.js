/*
 * Author: Charl Cronje
 * Date: 2018-03-24
 * Time: 04:57 AM
 * Some Time PHP functions created for JS
 */
'use strict';

phpJS.time = class {
    static timeSleepUntil(timestamp) {
        while(new Date() < timestamp * 1000) {
        }
        return true;
    }

    static sleep(seconds) {
        while(parseInt(seconds) * 1000 > 0) {
            setTimeout(function () {
                seconds = seconds - 1000;
                return true;
            },1000);
        }
    }
}