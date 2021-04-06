let markdown = require("markdown").markdown;
export default function (Vue) {
    Vue.filter("baseUrl", (url) => (_.startsWith(url, Vue.url.options.root) ? url.substr(Vue.url.options.root.length) : url));

    Vue.filter("trans", (id, parameters, domain, locale) => {
        return Vue.prototype.$trans(id, parameters, domain, locale);
    });

    Vue.filter("transChoice", (id, number, parameters, domain, locale) => Vue.prototype.$transChoice(id, number, parameters, domain, locale));

    Vue.filter("trim", {
        write(value) {
            return value.trim();
        },
    });

    Vue.filter("date", (date, format, timezone) => Vue.prototype.$date(date, format, timezone));

    Vue.filter("number", (number, fractionSize) => Vue.prototype.$number(number, fractionSize));

    Vue.filter("currency", (amount, currencySymbol, fractionSize) => Vue.prototype.$currency(amount, currencySymbol, fractionSize));

    Vue.filter("relativeDate", (date, options) => Vue.prototype.$relativeDate(date, options));

    Vue.filter("lowercase", (value) => (value || value === 0 ? value.toString().toLowerCase() : ""));

    Vue.filter("markdown", function (value) {
        return markdown.toHTML(value);
    });

    Vue.prototype.orderBy = orderBy;

    Vue.prototype.filterBy = filterBy;
}

/*
 * Vue2 Filters
 * ------------
 * https://github.com/freearhey/vue2-filters
 */

var toString = Object.prototype.toString;
var OBJECT_STRING = "[object Object]";

var util = {
    isObject: (obj) => {
        var type = typeof obj;
        return type === "function" || (type === "object" && !!obj);
    },
    isArray: (obj) => Array.isArray(obj),
    isPlainObject: (obj) => {
        return toString.call(obj) === OBJECT_STRING;
    },
    toArray: (list, start) => {
        start = start || 0;
        var i = list.length - start;
        var ret = new Array(i);
        while (i--) {
            ret[i] = list[i + start];
        }
        return ret;
    },
    convertArray: (value) => {
        if (util.isArray(value)) {
            return value;
        } else if (util.isPlainObject(value)) {
            // convert plain object to array.
            var keys = Object.keys(value);
            var i = keys.length;
            var res = new Array(i);
            var key;
            while (i--) {
                key = keys[i];
                res[i] = {
                    $key: key,
                    $value: value[key],
                };
            }
            return res;
        } else {
            return value || [];
        }
    },
    // obj,'1.2.3' -> multiIndex(obj,['1','2','3'])
    getPath: (obj, is) => multiIndex(obj, is.split(".")),
};

// obj,['1','2','3'] -> ((obj['1'])['2'])['3']
function multiIndex(obj, is) {
    return is.length ? multiIndex(obj[is[0]], is.slice(1)) : obj;
}

function contains(val, search) {
    var i;
    if (util.isPlainObject(val)) {
        var keys = Object.keys(val);
        i = keys.length;
        while (i--) {
            if (contains(val[keys[i]], search)) {
                return true;
            }
        }
    } else if (util.isArray(val)) {
        i = val.length;
        while (i--) {
            if (contains(val[i], search)) {
                return true;
            }
        }
    } else if (val != null) {
        return val.toString().toLowerCase().indexOf(search) > -1;
    }
}

function orderBy(arr) {
    var comparator = null;
    var sortKeys;
    arr = util.convertArray(arr);

    // determine order (last argument)
    var args = util.toArray(arguments, 1);
    var order = args[args.length - 1];
    if (typeof order === "number") {
        order = order < 0 ? -1 : 1;
        args = args.length > 1 ? args.slice(0, -1) : args;
    } else {
        order = 1;
    }

    // determine sortKeys & comparator
    var firstArg = args[0];
    if (!firstArg) {
        return arr;
    } else if (typeof firstArg === "function") {
        // custom comparator
        comparator = function (a, b) {
            return firstArg(a, b) * order;
        };
    } else {
        // string keys. flatten first
        sortKeys = Array.prototype.concat.apply([], args);
        comparator = function (a, b, i) {
            i = i || 0;
            return i >= sortKeys.length - 1 ? baseCompare(a, b, i) : baseCompare(a, b, i) || comparator(a, b, i + 1);
        };
    }

    function baseCompare(a, b, sortKeyIndex) {
        var sortKey = sortKeys[sortKeyIndex];
        if (sortKey) {
            if (sortKey !== "$key") {
                if (util.isObject(a) && "$value" in a) a = a.$value;
                if (util.isObject(b) && "$value" in b) b = b.$value;
            }
            a = util.isObject(a) ? util.getPath(a, sortKey) : a;
            b = util.isObject(b) ? util.getPath(b, sortKey) : b;
        }
        return a === b ? 0 : a > b ? order : -order;
    }

    // sort on a copy to avoid mutating original array
    return arr.slice().sort(comparator);
}

function filterBy(arr, search) {
    var arr = util.convertArray(arr);
    if (search == null) {
        return arr;
    }
    if (typeof search === "function") {
        return arr.filter(search);
    }
    // cast to lowercase string
    search = ("" + search).toLowerCase();
    var n = 2;
    // extract and flatten keys
    var keys = Array.prototype.concat.apply([], util.toArray(arguments, n));
    var res = [];
    var item, key, val, j;
    for (var i = 0, l = arr.length; i < l; i++) {
        item = arr[i];
        val = (item && item.$value) || item;
        j = keys.length;
        if (j) {
            while (j--) {
                key = keys[j];
                if ((key === "$key" && contains(item.$key, search)) || contains(util.getPath(val, key), search)) {
                    res.push(item);
                    break;
                }
            }
        } else if (contains(item, search)) {
            res.push(item);
        }
    }
    return res;
}
