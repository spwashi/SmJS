/**
 * Created by Sam Washington on 5/20/17.
 */

import {default as EventEmitter, EVENTS} from "./EventEmitter";
import SymbolStore from "./symbols/SymbolStore";
const _receive = (self, eventName, fn, once = true) => {
    let resolve = 'boon', reject;
    let func    = (...args) => {
        if (typeof fn === 'function') fn(...args);
        return resolve(args);
    };
    setTimeout(i => reject(eventName.Symbol), 50);
    const promise = new Promise((yes, no) => [resolve, reject] = [yes, no]);
    (once ? self.Events.once(eventName, func) : self.Events.on(eventName, func));
    return promise;
    
};
/**
 *
 */
class Std {
    /**
     * @param symbol
     * @return {SymbolStore}
     */
    static getSymbolStore(symbol) {
        if (typeof symbol === 'string') {
            let identifer = this === Std ? '[' : `[${this.name}]`;
            if (symbol.indexOf(identifer) !== 0) symbol = this.createName(symbol);
            symbol = Symbol.for(symbol);
        }
        return SymbolStore.init(symbol, null, symbol);
    }
    
    static resolve(symbol) {
        let symbolStore = this.getSymbolStore(symbol);
        let COMPLETE    = Std.EVENTS.item('init').COMPLETE;
        COMPLETE        = symbolStore.item(COMPLETE);
        return Std.receive(COMPLETE)
    }
    
    static get name() {return 'Std';}
    
    static createName(name) {
        name = name || Math.random().toString(36).substr(4, 6);
        return `[${this.name}].${name}`
    }
    
    /**
     * @param symbol This is some sort of identifier for this object
     */
    constructor(symbol) {
        /** @type {events.EventEmitter}  */
        this._Events = new EventEmitter(this);
        
        this._name = this.constructor.createName(symbol);
        if (typeof symbol !== 'symbol') symbol = Symbol.for(this._name);
        this._Symbol = symbol;
        
        /** @type {SymbolStore}  */
        this[EVENTS] = this.constructor.getSymbolStore(symbol);
        const BEGIN = Std.EVENTS.item('init').BEGIN;
        this.send(this.EVENTS.item(BEGIN).STATIC, this);
        /**
         *
         * @type {Promise}
         * @protected
         */
        this._parentPromise = Promise.resolve(this.complete(Std.name));
    }
    
    complete(name) {
        if (name === this.constructor.name) {
            const complete = Std.EVENTS.item('init').COMPLETE;
            return this.send(this.EVENTS.item(complete).STATIC, this);
        }
    }
    
    get Symbol() { return this._Symbol; }
    
    //region Events/EVENTS
    /**
     * @return {events.EventEmitter}
     * @constructor
     */
    get Events() { return this._Events; }
    
    /**
     * @return {SymbolStore}
     */
    get EVENTS() { return this[EVENTS]; }
    
    /**
     * @return {EventEmitter|*|events.EventEmitter}
     */
    static get Events() { return this._Events || (this._Events = new EventEmitter(this)) }
    
    /**
     * @return {SymbolStore}
     */
    static get EVENTS() { return this[EVENTS] || (this[EVENTS] = new SymbolStore(Symbol.for(this.name))); }
    
    //endregion
    
    //region Send/Receive
    static send(eventName, ...args) { this.Events.emit(eventName, ...args); }
    
    static receive(eventName, fn, once = true) { return _receive(this, ...arguments); }
    
    receive(eventName, fn, once = true) {return _receive(this, ...arguments);}
    
    send(eventName, ...args) {
        this._Events.emit(eventName, ...args);
        this.constructor.send(eventName, ...args);
        if (this.constructor !== Std) Std.send(eventName, ...args);
        return Promise.resolve(this);
    }
    
    //endregion
}
export default Std;
export {Std};