'use strict';

core.app = class {
    constructor() {

    }

    numberFormat(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number;
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
        var s = '';

        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };

        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }

    round(value, precision, mode) {
        var m, f, isHalf, sgn; // helper variables
        // making sure precision is integer
        precision |= 0;
        m = Math.pow(10, precision);
        value *= m;
        // sign of the number
        sgn = (value > 0) | -(value < 0);
        isHalf = value % 1 === 0.5 * sgn;
        f = Math.floor(value);
        if (isHalf) {
            switch (mode) {
                case 'PHP_ROUND_HALF_DOWN':
                    // rounds .5 toward zero
                    value = f + (sgn < 0);
                break;
                case 'PHP_ROUND_HALF_EVEN':
                    // rounds .5 towards the next even integer
                    value = f + (f % 2 * sgn);
                break;
                case 'PHP_ROUND_HALF_ODD':
                    // rounds .5 towards the next odd integer
                    value = f + !(f % 2);
                break;
                default:
                    // rounds .5 away from zero
                    value = f + (sgn > 0);
            }
        }
        return (isHalf ? value : Math.round(value)) / m
    }
};