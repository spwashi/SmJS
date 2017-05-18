/**
 * Created by Sam Washington on 5/7/17.
 */
export default class StdEventDescriptor {
    static init() { return new this(...arguments); }
    
    constructor(name, ...args) {
        this._name      = name;
        this._arguments = args;
    }
    
    set name(name) {
        this._name = name;
    }
    
    get name() {
        return this._name;
    }
    
    matches(name, ..._args) {
        if (this._name && name !== this._name) return false;
        let a1 = this._arguments;
        let a2 = _args;
        return a1.length === a2.length && a1.every((v, i) => v === a2[i])
    }
}