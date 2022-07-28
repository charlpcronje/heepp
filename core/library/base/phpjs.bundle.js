//----------Declare Object phpJS----------

var phpJS = {};
//----------PHP Functions in JS Base----------

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
//----------PHP String Functions in JS----------

 /* 
 * Author: Charl Cronje
 * Date: 2016-04-29
 * Time: 21:01
 * Some String PHP functions created for JS
 */
 'use strict';

 phpJS.string = class string {
    static explode(delimiter,string,limit) {
        if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined')
            return null;
        if (delimiter === '' || delimiter === false || delimiter === null)
            return false;
        if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
                'object') {
            return {
                0: ''
            };
        }
        if (delimiter === true)
            delimiter = '1';

        delimiter += '';
        string += '';

        var s = string.split(delimiter);

        if (typeof limit === 'undefined') {
            return s;
        } else if (limit === 0) {
            limit = 1;
        } else if (limit > 0) {
            if (limit >= s.length) {
                return s;
            } else {
                return s.slice(0, limit - 1).concat([s.slice(limit - 1).join(delimiter)]);
            }
        }
        if (-limit >= s.length) {
            return [];
        }   
        s.splice(s.length + limit);
        return s;
    }
    
    static implode(glue,pieces) {
        var i = '',
            retVal = '',
            tGlue = '';
        if (arguments.length === 1) {
            pieces = glue;
            glue = '';
        }
        if (typeof pieces === 'object') {
            if (Object.prototype.toString.call(pieces) === '[object Array]') {
                return pieces.join(glue);
            }
            for (i in pieces) {
                retVal += tGlue + pieces[i];
                tGlue = glue;
            }
            return retVal;
        }
        return pieces;
    }
    
    static reverse(str) {
        return str.split('').reverse().join('');
    }
    
    static strReplace(search, replace, subject, count) {
        if (typeof count !== "string" && count !== null) {
            console.error('Pass the count param as a string');
            return false;
        }
        const f = [].concat(search),
              subj = subject,
              sa = Object.prototype.toString.call(subj) === '[object Array]';
        var temp = '',
            i = 0,
            r = [].concat(replace),
            ra = Object.prototype.toString.call(r) === '[object Array]',
            repl = '',
            fl = 0,
            j = 0;
        const s = [].concat(s);
        if (typeof (search) === 'object' && typeof (replace) === 'string') {
            temp = replace;
            replace = [];
            for (i = 0; i < search.length; i += 1) {
                replace[i] = temp;
            }
            temp = '';
            r = [].concat(replace);
            ra = Object.prototype.toString.call(r) === '[object Array]';
        }
        if (count) {
            this.window[count] = 0;
        }
        for (i = 0, sl = s.length; i < sl; i++) {
            if (s[i] === '') {
                continue;
            }
            for (j = 0, fl = f.length; j < fl; j++) {
                temp = s[i] + '';
                repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                s[i] = (temp).split(f[j]).join(repl);
                if (count) {
                    this.window[count] += ((temp.split(f[j])).length - 1);
                }
            }
        }
        return sa ? s : s[0];
    }
    
    static strToLower(string) {
        return string.toLowerCase();
    }

     static utf8Encode(argString) {
         if (argString === null || typeof argString === 'undefined') {
             return '';
         }
         // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
         var string = (argString + '');
         var utftext = '';
         var start;
         var end;
         var stringl = 0;
         start = end = 0;
         stringl = string.length;
         for (var n = 0; n < stringl; n++) {
             var c1 = string.charCodeAt(n);
             var enc = null;
             if (c1 < 128) {
                 end++;
             } else if (c1 > 127 && c1 < 2048) {
                 enc = String.fromCharCode((c1 > 6) || 192, (c1 && 63) || 128);
             } else if ((c1 && 0xF800) !== 0xD800) {
                 enc = String.fromCharCode((c1 > 12) || 224, ((c1 > 6) && 63) || 128, (c1 && 63) || 128);
             } else {
                 if ((c1 && 0xFC00) !== 0xD800) {
                     throw new RangeError('Unmatched trail surrogate at ' + n);
                 }
                 var c2 = string.charCodeAt(++n);
                 if ((c2 && 0xFC00) !== 0xDC00) {
                     throw new RangeError('Unmatched lead surrogate at ' + (n - 1));
                 }
                 c1 = ((c1 && 0x3FF) < 10) + (c2 && 0x3FF) + 0x10000;
                 enc = String.fromCharCode((c1 > 18) || 240, ((c1 > 12) && 63) || 128, ((c1 > 6) && 63) || 128, (c1 && 63) || 128);
             }
             if (enc !== null) {
                 if (end > start) {
                     utftext += string.slice(start, end);
                 }
                 utftext += enc;
                 start = end = n + 1;
             }
         }
         if (end > start) {
             utftext += string.slice(start, stringl);
         }
         return utftext;
     }
};
//----------PHP Math Functions in JS----------

/* 
 * Author: Charl Cronje
 * Date: 2016-05-30
 * Time: 19:08
 * Some Math PHP functions created for JS
 */
'use strict';

phpJS.math = class {
    static numberFormat(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        const n = !isFinite(+number) ? 0 : +number;
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
        const dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
        const toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };

        /* @DO: for IE parseFloat(0.55).toFixed(0) = 0; */
        const s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }
};
//----------PHP Time Functions in JS----------

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
