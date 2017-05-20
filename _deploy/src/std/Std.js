/**
 * Created by Sam Washington on 5/20/17.
 */

import {default as EventEmitter, EVENTS} from "./EventEmitter";
import SymbolStore from "./symbols/SymbolStore";
class Std {
    static get name() {return 'Std';}
    
    constructor(config) {
        /** @type {events.EventEmitter}  */
        this._Events = new EventEmitter(this);
        this._Symbol = Symbol(this.constructor.name);
        /** @type {SymbolStore}  */
        this[EVENTS] = new SymbolStore(this._Symbol, null, this._Symbol);
    }
    
    /**
     *
     * @return {events.EventEmitter}
     * @constructor
     */
    get Events() {
        return this._Events;
    }
    
    /**
     *
     * @return {SymbolStore}
     */
    get EVENTS() {
        return this[EVENTS];
    }
    
    get Symbol() {
        return this._Symbol;
    }
    
    send(eventName, ...args) {
        this._Events.emit(eventName, ...args);
        return Promise.resolve(this);
    }
    
    receive(eventName, fn) {
        let resolve, reject;
        
        this._Events.on(eventName, () => resolve(...arguments));
        if (typeof fn === 'function')
            this._Events.once(eventName, fn);
        return new Promise((yes, no) => {
            resolve = yes;
            reject  = no;
        })
    }
}
export default Std;
export {Std};