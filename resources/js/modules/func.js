function leftZeroPad(n) {
    if (Number.isInteger(n)) {
        return undefined;
    }
    const isNegative = n < 0;
    const number = isNegative ? -1 * n : n;
    return (isNegative ? '-' : '') + (number < 10 ? '0' + number : number);
}
function rightZeroPad(n) {
    if (isNaN(n)) {
        return undefined;
    }
    const isNegative = n < 0;
    const number = isNegative ? -1 * n : n;
    if (Number.isInteger(number)) {
        return (isNegative ? '-' : '') + number + '.00';
    }
    const numLen = number.toString();
    const decimalLen = numLen.indexOf('.');
    const fractionLen = numLen.length - decimalLen - 1;
    return (isNegative ? '-' : '') + (decimalLen === 0 ? '0' : '') + (fractionLen < 2 ? number + '0' : number);
}
function daysBetweenTwoDates(date1, date2) {
    return moment(date1, 'DD-MM-YYYY').diff(moment(date2, 'DD-MM-YYYY'), 'days');
}
function isValidNumber(input) {
    // Regular expression to match a number without "-" or "+" or "e"
    var numberPattern = /^[\d.]+$/;

    return numberPattern.test(input);
}
function isValidPosNumber(input) {
    // Regular expression to match a positive integer
    var numberPattern = /^[1-9]\d*$/;

    return numberPattern.test(input);
}
export { leftZeroPad, rightZeroPad, isValidNumber, isValidPosNumber, daysBetweenTwoDates };
