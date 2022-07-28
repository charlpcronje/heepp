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