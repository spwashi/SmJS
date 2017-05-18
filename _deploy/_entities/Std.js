/**
 * Created by Sam Washington on 5/4/17.
 */
import {_obj_filter, _to_json_and_back, _to_string} from "./util/index";
import {$Events$, EVENTS} from "./events/index";
import {SYMBOL} from "./symbols/index";
import StdEventEmitter from "./events/StdEventEmitter";

class Std {
    /**
     *
     * @param args
     * @return {Std|this}
     */
    static init(...args) {
        return new this(...args);
    }
    
    constructor(_config = {}) {
        if (typeof _config !== "object") throw new Error("Can only construct with objects");
        this._exclude   = new Set;
        this._referrers = new Set;
        /** @type {this|Std}  */
        this.static = this.constructor;
        this.static._initClass();
        let Events = new StdEventEmitter(this);
        ///////////////////////////////////////////////////
        /**
         *
         * @type {StdEventEmitter}
         * @protected
         */
        this._Events = Events;
        this._name         = _config._name || null;
        this._inheritables = _config.inheritables || this.static.inheritables;
        Object.assign(this, _to_json_and_back(this.static.defaults));
        //////////////////////////////////////////////////
        this.createIdentity(_config);
        Events.emitOnce(this.static[EVENTS].init.self[SYMBOL], this, 'HERE');
    }
    
    static _initClass() {
        this._initializedClasses = this._initializedClasses || new Set;
        if (this._initializedClasses.has(this)) return null;
        this[EVENTS]      = $Events$;
        this.static       = this;
        this._Events      = this._Events || new StdEventEmitter(this);
        this._object_type = this._object_type || Math.random().toString(36).substr(7);
        this[SYMBOL]      = Symbol.for(`object#${this._object_type}`);
        this._initializedClasses.add(this);
        this._Events.emitOnce(this.static[EVENTS]
                                  .init
                                  ._class
                                  .item(this[SYMBOL])
                                  [SYMBOL], this);
        return true;
    }
    
    then_do(fn) {
        if (typeof fn === "function") fn(this);
        return this;
    }
    
    createIdentity(_config = {}) {
        if (_config._identity) {
            this._identity = _config._identity
        } else if (_config._name) {
            this._identity = `[${this.static._object_type}].${_config._name}`;
        } else {
            this._identity = Math.random().toString(36).substr(7);
        }
        this.Events.emitOnce(this.static[EVENTS].init.identity[SYMBOL], this);
        Object.assign(this, _config);
    }
    
    //
    
    inherit(item) {
        if (typeof item !== "object" || !item) return this;
        
        let _inheritables = item;
        
        if (item instanceof Std) {
            this._referrers.add(item);
            _inheritables = item.inheritable_items;
        } else {
            _inheritables = _to_json_and_back(_inheritables);
        }

//        console.log(`${this._identity} -\n ${item instanceof Std ? item._identity : ''}\n`, _inheritables);
        
        _inheritables = _obj_filter(_inheritables, (val, key) => key[0] !== '_');
        Object.assign(this, _inheritables);
        return this;
    }
    
    toJSON() {
        return Object.assign({}, this.defaults, {
            name:        this._name,
            _identity:   this._identity,
            _references: [...this._referrers].map(_ => _._identity)
        });
    }
    
    static inherit(_config, item) {
        return this.init(_config).inherit(item);
    }
    
    //
    
    static get defaults() {
        return {};
    }
    
    when() {
        return this.Events ? this.Events.when(...arguments) : Promise.reject('No events initialized');
    }
    
    static when() {
        return this.Events ? this.Events.when(...arguments) : Promise.reject('No events initialized');
    }
    
    static get inheritables() {
        return Object.keys(this.defaults || {}).filter(item => typeof item === "string" && item[0] !== '_')
    }
    
    get defaults() {
        let obj  = {},
            keys = Object.keys(this.static.defaults || {});
        keys.forEach(key => obj[key] = this[key]);
        return obj;
    }
    
    get inheritable_items() {
        let keys = Object.keys(this.static.defaults || {}).filter((item) => {
            if (typeof this[item] === "undefined") return false;
            if (typeof item !== "string") return false;
            if (this._exclude.has(item)) return false;
            if (this[item] === this.static.defaults[item]) return false;
            
            if (item[0] !== '_') return true;
        });
        let obj  = {};
        keys.forEach(key => obj[key] = this[key]);
        return obj;
    }
    
    /**
     *
     * @return {Std|this}
     * @protected
     */
    _complete() {
        this.Events.emitOnce(this.static[EVENTS].complete.self[SYMBOL], this);
        return this;
    }
    
    set exclude(arr) {
        if (typeof arr === "string") arr = [arr];
        if (!Array.isArray(arr)) throw new Error("Can only exclude arrays");
        arr.forEach(item => this._exclude.add(item));
    }
    
    get inheritables() {
        return this._inheritables || [];
    }
    
    get Events() {
        return this._Events;
    }
    
    get complete() {
        return this.Events.when(this.static[EVENTS].complete.self[SYMBOL]).then(_ => this);
    }
    
    /**
     *
     * @return {StdEventEmitter}
     */
    static get Events() {
        this._initClass(this);
        return this._Events;
    }
}
Std.Events.bindEvent('*', StdEventEmitter.Events);

let s = [];

StdEventEmitter.Events.on('*', (e_name, reference) => {
    if (reference instanceof Std)
        s.push(`${_to_string(e_name)} --- ${reference._identity}`);
});
export default Std;
export {Std}