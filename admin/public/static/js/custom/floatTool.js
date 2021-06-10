// javascript在计算浮点数（小数）不准确，解决方案
// 10 阅读 作者：K_Martin 来源：开源中国 2020-03-09
// https://developer.aliyun.com/article/715839
// 方案来自网络，实现简单，便于做加减乘除使用，由于项目临时要用记录下
//
// 如需要更加复杂的计算类库，可以考虑 math.js等知名类库

// 对于小数，前端出现问题的几率还是很多的，尤其在一些电商网站涉及到金额等数据。
// 解决方式：把小数放到位整数（乘倍数），再缩小回原来倍数（除倍数）转换成整数后的运算结果 不能超过 Math.pow(2,53)

/**
 * floatTool 包含加减乘除四个方法，能确保浮点数运算不丢失精度
 *
 * 我们知道计算机编程语言里浮点数计算会存在精度丢失问题（或称舍入误差），其根本原因是二进制和实现位数限制有些数无法有限表示
 * 以下是十进制小数对应的二进制表示
 *      0.1 >> 0.0001 1001 1001 1001…（1001无限循环）
 *      0.2 >> 0.0011 0011 0011 0011…（0011无限循环）
 * 计算机里每种数据类型的存储是一个有限宽度，比如 JavaScript 使用 64 位存储数字类型，因此超出的会舍去。舍去的部分就是精度丢失的部分。
 *
 * ** method **
 *  add / subtract / multiply /divide
 *
 * ** explame **
 *  0.1 + 0.2 == 0.30000000000000004 （多了 0.00000000000004）
 *  0.2 + 0.4 == 0.6000000000000001  （多了 0.0000000000001）
 *  19.9 * 100 == 1989.9999999999998 （少了 0.0000000000002）
 *
 * floatObj.add(0.1, 0.2) >> 0.3
 * floatObj.multiply(19.9, 100) >> 1990
 *
 */
var floatTool = function() {

    /*
     * 判断obj是否为一个整数
     */
    function isInteger(obj) {
        return Math.floor(obj) === obj;
    }

    /*
     * 将一个浮点数转成整数，返回整数和倍数。如 3.14 >> 314，倍数是 100
     * @param floatNum {number} 小数
     * @return {object}
     *   {times:100, num: 314}
     */
    function toInteger(floatNum) {
        var ret = {times: 1, num: 0};
        if (isInteger(floatNum)) {
            ret.num = floatNum;
            return ret;
        }
        var strfi  = floatNum + '';
        var dotPos = strfi.indexOf('.');
        var len    = strfi.substr(dotPos+1).length;
        var times  = Math.pow(10, len);
        var intNum = parseInt(floatNum * times + 0.5, 10);
        ret.times  = times;
        ret.num    = intNum;
        return ret;
    }

    /*
     * 核心方法，实现加减乘除运算，确保不丢失精度
     * 思路：把小数放大为整数（乘），进行算术运算，再缩小为小数（除）
     *
     * @param a {number} 运算数1
     * @param b {number} 运算数2
     * @param digits {number} 精度，保留的小数点数，比如 2, 即保留为两位小数
     * @param op {string} 运算类型，有加减乘除（add/subtract/multiply/divide）
     *
     */
    function operation(a, b, op) {
        var o1 = toInteger(a);
        var o2 = toInteger(b);
        var n1 = o1.num;
        var n2 = o2.num;
        var t1 = o1.times;
        var t2 = o2.times;
        var max = t1 > t2 ? t1 : t2;
        var result = null;
        switch (op) {
            case 'add':
                if (t1 === t2) { // 两个小数位数相同
                    result = n1 + n2;
                } else if (t1 > t2) { // o1 小数位 大于 o2
                    result = n1 + n2 * (t1 / t2);
                } else { // o1 小数位 小于 o2
                    result = n1 * (t2 / t1) + n2;
                }
                return result / max;
            case 'subtract':
                if (t1 === t2) {
                    result = n1 - n2;
                } else if (t1 > t2) {
                    result = n1 - n2 * (t1 / t2);
                } else {
                    result = n1 * (t2 / t1) - n2;
                }
                return result / max;
            case 'multiply':
                result = (n1 * n2) / (t1 * t2);
                return result;
            case 'divide':
                return result = function() {
                    var r1 = n1 / n2;
                    var r2 = t2 / t1;
                    return operation(r1, r2, 'multiply');
                }();
        }
    }

    // 加减乘除的四个接口
    function add(a, b) {
        return operation(a, b, 'add');
    }
    function subtract(a, b) {
        return operation(a, b, 'subtract');
    }
    function multiply(a, b) {
        return operation(a, b, 'multiply');
    }
    function divide(a, b) {
        return operation(a, b, 'divide');
    }

    // exports
    return {
        add: add,
        subtract: subtract,
        multiply: multiply,
        divide: divide
    };
}();

// 使用方法：
// floatTool.add(a,b);//相加
// floatTool.subtract(a,b);//相减
// floatTool.multiply(a,b);//相乘
// floatTool.divide(a,b);//相除


// 超大整数
// 虽然运算结果不超过Math.pow(2,53)的整数（9007199254740992）也可以使用上面的方法，
// 但是如果就是有超过的呢，实际场景中可能会是一些批次号、号段之类的需求，这里我也找到了一个解决方案，直接上代码。
//
// 在线运算：https://www.shen.ee/math.html

function compare(p, q) {
    while (p[0] === '0') {
        p = p.substr(1);
    }
    while (q[0] === '0') {
        q = q.substr(1);
    }
    if (p.length > q.length) {
        return 1;
    } else if (p.length < q.length) {
        return -1;
    } else {
        let i = 0;
        let a, b;
        while (1) {
            a = parseInt(p.charAt(i));
            b = parseInt(q.charAt(i));
            if (a > b) {
                return 1;
            } else if (a < b) {
                return -1;
            } else if (i === p.length - 1) {
                return 0;
            }
            i++;
        }
    }
}

function divide(A, B) {
    let result = [];
    let max = 9;
    let point = 5;
    let fill = 0;
    if (B.length - A.length > 0) {
        point += fill = B.length - A.length;
    }
    for (let i = 0; i < point; i++) {
        A += '0';
    }
    let la = A.length;
    let lb = B.length;

    let b0 = parseInt(B.charAt(0));
    let Adb = A.substr(0, lb);
    A = A.substr(lb);
    let temp, r;
    for (let j = 0; j < la - lb + 1; j++) {
        while (Adb[0] === '0') {
            Adb = Adb.substr(1);
        }
        if (Adb.length === lb) {
            max = Math.ceil((parseInt(Adb.charAt(0)) + 1) / b0); // 不可能取到这个最大值,1<= max <= 10
        } else if (Adb.length > lb) {
            max = Math.ceil((parseInt(Adb.substr(0, 2)) + 1) / b0);
        } else {
            result.push(0);
            Adb += A[0];
            A = A.substr(1);
            continue;
        }
        for (let i = max - 1; i >= 0; i--) {
            if (i === 0) {
                result.push(0);
                Adb += A[0];
                A = A.substr(1);
                break;
            } else {
                temp = temp || multiply(B, i + '');
                r = compare(temp, Adb);
                if (r === 0 || r === -1) {
                    result.push(i);
                    if (r) {
                        Adb = reduce(Adb, temp);
                        Adb += A[0];
                    } else {
                        Adb = A[0];
                    }
                    A = A.substr(1);
                    break;
                } else {
                    temp = reduce(temp, B);
                }
            }
        }
        temp = 0;
    }
    for (let i = 0; i < fill; i++) {
        result.unshift('0');
    }
    result.splice(result.length - point, 0, '.');

    if (!result[0] && result[1] !== '.') {
        result.shift();
    }

    point = false;
    let position = result.indexOf('.');

    for (let i = position + 1; i < result.length; i++) {
        if (result[i]) {
            point = true;
            break;
        }
    }
    if (!point) {
        result.splice(position);
    }

    result = result.join('');
    return result;
}

function multiply(A, B) {
    let result = [];
    (A += ''), (B += '');
    const l = -4; // 以支持百万位精确运算，但速度减半

    let r1 = [],
        r2 = [];
    while (A !== '') {
        r1.unshift(parseInt(A.substr(l)));
        A = A.slice(0, l);
    }
    while (B !== '') {
        r2.unshift(parseInt(B.substr(l)));
        B = B.slice(0, l);
    }
    let index, value;
    for (let i = 0; i < r1.length; i++) {
        for (let j = 0; j < r2.length; j++) {
            value = 0;
            if (r1[i] && r2[j]) {
                value = r1[i] * r2[j];
            }
            index = i + j;
            if (result[index]) {
                result[index] += value;
            } else {
                result[index] = value;
            }
        }
    }
    for (let i = result.length - 1; i > 0; i--) {
        result[i] += '';
        if (result[i].length > -l) {
            result[i - 1] += parseInt(result[i].slice(0, l));
            result[i] = result[i].substr(l);
        }
        while (result[i].length < -l) {
            result[i] = '0' + result[i];
        }
    }
    if (result[0]) {
        result = result.join('');
    } else {
        result = '0';
    }
    return result;
}

function add(A, B) {
    let result = [];
    (A += ''), (B += '');
    const l = -15;
    while (A !== '' && B !== '') {
        result.unshift(parseInt(A.substr(l)) + parseInt(B.substr(l)));
        A = A.slice(0, l);
        B = B.slice(0, l);
    }
    A += B;

    for (let i = result.length - 1; i > 0; i--) {
        result[i] += '';
        if (result[i].length > -l) {
            result[i - 1] += 1;
            result[i] = result[i].substr(1);
        } else {
            while (result[i].length < -l) {
                result[i] = '0' + result[i];
            }
        }
    }

    while (A && (result[0] + '').length > -l) {
        result[0] = (result[0] + '').substr(1);
        result.unshift(parseInt(A.substr(l)) + 1);
        A = A.slice(0, l);
    }

    if (A) {
        while ((result[0] + '').length < -l) {
            result[0] = '0' + result[0];
        }
        result.unshift(A);
    }

    if (result[0]) {
        result = result.join('');
    } else {
        result = '0';
    }

    return result;
}

function reduce(A, B) {
    let result = [];
    (A += ''), (B += '');
    while (A[0] === '0') {
        A = A.substr(1);
    }
    while (B[0] === '0') {
        B = B.substr(1);
    }
    const l = -15;
    let N = '1';
    for (let i = 0; i < -l; i++) {
        N += '0';
    }
    N = parseInt(N);
    while (A !== '' && B !== '') {
        result.unshift(parseInt(A.substr(l)) - parseInt(B.substr(l)));
        A = A.slice(0, l);
        B = B.slice(0, l);
    }
    if (A !== '' || B !== '') {
        let s = B === '' ? 1 : -1;
        A += B;
        while (A !== '') {
            result.unshift(s * parseInt(A.substr(l)));
            A = A.slice(0, l);
        }
    }
    while (result.length !== 0 && result[0] === 0) {
        result.shift();
    }
    let s = '';
    if (result.length === 0) {
        result = 0;
    } else if (result[0] < 0) {
        s = '-';
        for (let i = result.length - 1; i > 0; i--) {
            if (result[i] > 0) {
                result[i] -= N;
                result[i - 1]++;
            }
            result[i] *= -1;
            result[i] += '';
            while (result[i].length < -l) {
                result[i] = '0' + result[i];
            }
        }
        result[0] *= -1;
    } else {
        for (let i = result.length - 1; i > 0; i--) {
            if (result[i] < 0) {
                result[i] += N;
                result[i - 1]--;
            }
            result[i] += '';
            while (result[i].length < -l) {
                result[i] = '0' + result[i];
            }
        }
    }

    if (result) {
        while ((result[0] = parseInt(result[0])) === 0) {
            result.shift();
        }
        result = s + result.join('');
    }
    return result;
}

// 使用方法：不可使用负数，参数最好使用字符串
// divide(A,B)	// 除法
// multiply(A,B)	//乘法
// add(A,B)	//加法
// reduce(A,B)	//减法

// toFixed 的修复
// 在Firefox / Chrome中，toFixed并不会对于最后一位是5的如愿以偿的进行四舍五入。
// 1.35.toFixed(1) // 1.4 正确
// 1.335.toFixed(2) // 1.33  错误
// 1.3335.toFixed(3) // 1.333 错误
// 1.33335.toFixed(4) // 1.3334 正确
// 1.333335.toFixed(5)  // 1.33333 错误
// 1.3333335.toFixed(6) // 1.333333 错误
// Firefox 和 Chrome的实现没有问题，根本原因还是计算机里浮点数精度丢失问题。
//
// 修复方式：
// function toFixed(num, s) {
//     var times = Math.pow(10, s)
//     var des = num * times + 0.5
//     des = parseInt(des, 10) / times
//     return des + ''
// }
