/**
 * Created by Sam Washington on 5/4/17.
 */
/**
 * Created by Sam Washington on 4/29/17.
 */
const util = require('util');

export let _log      = (...e) => console.log(...e);
export let _log_then = (...e) => {
    _log(...e);
    return e[0];
};

/**
 * Filter an object's properties
 * @param obj
 * @param predicate
 */
export let _obj_filter = (obj, predicate) =>
    Object.keys(obj)
          .filter(key => predicate(obj[key], key))
          .reduce((res, key) => (res[key] = obj[key], res), {});
export const _check_same_array = (a1, a2) => a1 && a2 && a1.length === a2.length && a1.every((v, i) => v === a2[i]);
export const _to_string        = e => typeof e === 'symbol' ? e.toString() : String(e);
export let _to_json_and_back   = e => JSON.parse(JSON.stringify(e));
export let _err                = (e) => console.error(e, e.stack.split('\n'));
/**
 * Iterate over an object
 * @param obj
 */
export function* _entries(obj) {
    for (let key of Object.keys(obj)) yield [key, obj[key]];
}