/* 
 * Author: Charl Cronje
 * Date: 2016-11-29
 * Time: 21:01
 * Some General PHP functions created for JS
 */
'use strict';

phpJS.base = class {
    static uniqId(prefix, more_entropy) {
        if (typeof prefix === 'undefined') {
            prefix = '';
        }
        var retId;
        var formatSeed = function (seed, reqWidth) {
            seed = window.parseInt(seed, 10).toString(16);
            if (reqWidth < seed.length) {
                return seed.slice(seed.length - reqWidth);
            }
            if (reqWidth > seed.length) {
                return Array(1 + (reqWidth - seed.length)).join('0') + seed;
            }
            return seed;
        };
        if (!this.php_js) {
            this.php_js = {};
        }
        if (!this.php_js.uniqidSeed) {
            this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
        }
        this.php_js.uniqidSeed++;
        retId = prefix;
        retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
        retId += formatSeed(this.php_js.uniqidSeed, 5);
        if (more_entropy) {
            retId += (Math.random() * 10).toFixed(8).toString();
        }
        return retId;
    }
};