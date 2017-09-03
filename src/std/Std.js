/**
 * Created by Sam Washington on 5/20/17.
 */

import {default as EventEmitter, EVENTS} from "./EventEmitter";
import SymbolStore from "./symbols/SymbolStore";
import TimeoutError from "../errors/TimeoutError";

const ATTRIBUTE = SymbolStore.$_$.item('_attribute_').Symbol;

/** Standard class */
class Std {
    //region Getters and Setters
    static smID = 'Std';
    
    //region Initialization
    /**
     * @param identifier This is some sort of identifier for this object
     */
    constructor(identifier) {
        /** @type {events.EventEmitter}  */
        this._Events = new EventEmitter(this);
        this._originalName = identifier;
        
        //region Status
        this._isAvailable = false;
        this._isComplete  = false;
        //endregion
    
        this.smID = this.constructor.createName(identifier);
        if (typeof identifier !== 'symbol') identifier = Symbol.for(this.smID);
        this._Symbol      = identifier;
        const symbolStore = this.constructor.getSymbolStore(identifier);
        /**
         * @type {SymbolStore}
         * @protected
         */
        this._symbolStore = symbolStore;
        /**
         * Register Attributes as a Map
         * @type {Map}
         */
        this._attributes = new Map;
        /**
         * Refers to the identifiers of the events emitted by this class
         * @type {SymbolStore}
         */
        this[EVENTS] = symbolStore.item(EVENTS);
        /** @type {SymbolStore} The Event that marks the beginning of this object's initialization */
        const BEGIN = Std.EVENTS.item('init').BEGIN;
        this.send(this.EVENTS.item(BEGIN).STATIC, this);
    }
    
    /**
     * @return {EventEmitter|*|events.EventEmitter}
     */
    static get Events() { return this._Events || (this._Events = new EventEmitter(this)) }
    
    /**
     * @return {SymbolStore}
     */
    static get EVENTS() { return this[EVENTS] || (this[EVENTS] = new SymbolStore(Symbol.for(this.smID))); }
    
    //endregion
    
    get symbolStore() {
        return this._symbolStore;
    }
    
    /**
     * Get this object when it is available.
     *
     * @return {this}
     */
    get available() {
        return this.receive(this.EVENTS.item(Std.EVENTS.item('available'))).then(i => i[1] || null);
    }
    
    get isAvailable() {return this._isAvailable}
    
    get isComplete() {return this._isComplete}
    
    /**
     * Get the Symbol that identifies this object
     * @return {Symbol}
     * @constructor
     */
    get Symbol() { return this._Symbol; }
    
    get symbolName() {return this._Symbol.toString();}
    
    get originalName() {return this._originalName}
    
    /**
     * @return {events.EventEmitter}
     * @constructor
     */
    get Events() { return this._Events; }
    
    /**
     * @return {SymbolStore}
     */
    get EVENTS() { return this[EVENTS]; }
    
    //endregion
    //region Getters
    
    static createName(name): string {
        name = name || Math.random().toString(36).substr(4, 6);
        return `[${this.smID}]${name}`
    }
    
    /**
     * Create an instance of this class. Allows us to manage subclasses as well.
     * @method Sm.Std.factory()
     * @return {Std}
     */
    static factory() {
        const ctor = this;
        return this.init(...arguments);
    }
    
    /**
     *
     * @param {string|symbol}   identifier
     * @param {{_id?:string}}              config
     * @return {Promise<Std>}
     */
    static init(identifier, config = {}) {
        if (typeof identifier === "object" && identifier) {
            config     = identifier;
            identifier = null;
        }
        const self                 = new this(...arguments);
        config._id                 = config._id || identifier;
        const promise              = self
            .initialize(config)
            .then(self => self._sendInitComplete(this.smID));
        promise.initializingObject = self;
        return promise;
    }
    
    /**
     * @param symbol
     * @return {SymbolStore}
     */
    static getSymbolStore(symbol) {
        /** @type {string} item If we are retrieving a property of this item */
        let item;
        if (typeof symbol === 'string') {
            let identifer = this === Std ? '[' : `[${this.smID}]`;
            if (symbol.indexOf('|') > 0) [symbol, item] = symbol.split('|') || null;
            if (symbol.indexOf(identifer) !== 0) symbol = this.createName(symbol);
            symbol = Symbol.for(symbol);
            
        }
        const symbolStore = SymbolStore.init(symbol, null, symbol);
        if (!item) return symbolStore;
        else return symbolStore.item(ATTRIBUTE).item(item);
    }
    
    static resolve(symbol) {
        let symbolStore = this.getSymbolStore(symbol);
        
        // If we are trying to resolve something that has been registered as an attribute
        const is_property = symbolStore.family.has(ATTRIBUTE);
        const COMPLETE    = is_property ? symbolStore : symbolStore.item(EVENTS)
                                                                   .item(Std.EVENTS.item('init').COMPLETE);
        return Std.receive(COMPLETE)
    }
    
    static available(symbol) {
        let symbolStore = this.getSymbolStore(symbol);
        
        // If we are trying to resolve something that has been registered as an attribute
        const is_property = symbolStore.family.has(ATTRIBUTE);
        const COMPLETE    = is_property ? symbolStore : symbolStore.item(EVENTS)
                                                                   .item(Std.EVENTS.item('available'));
        return Std.receive(COMPLETE)
    }
    
    //region Send/Receive
    static send(eventName, ...args) { this.Events.emit(eventName, ...args); }
    
    /**
     *
     * @param self
     * @param eventName
     * @param fn
     * @param once
     * @return {Promise}
     * @private
     */
    static _receive(self, eventName, fn, once = true) {
        let resolve, reject;
        let func = (...args) => {
            if (typeof fn === 'function') fn(...args);
            return resolve(args);
        };
        
        const granted_time = 500;
        const timeoutError = new TimeoutError('Timeout in ' + (self.symbolName || self.smID), eventName, granted_time);
        setTimeout(i => {
            return reject(timeoutError)
        }, granted_time);
        
        const promise = new Promise((yes, no) => [resolve, reject] = [yes, no]);
        (once ? self.Events.once(eventName, func) : self.Events.on(eventName, func));
        return promise;
    };
    
    //endregion
    
    static receive(eventName, fn, once = true) { return this._receive(this, ...arguments); }
    
    registerAttribute(name, attribute) {
        const propertySymbolStore = this._symbolStore.item(ATTRIBUTE).item(name);
        this._attributes.set(name, attribute);
        return this.send(propertySymbolStore.STATIC, attribute);
    }
    
    resolve(symbol) {
        return Std.receive(this._symbolStore.item(ATTRIBUTE).item(symbol));
    }
    
    initialize(config) {
        return Promise.resolve(this);
    }
    
    /**
     * Emit an event saying that we are done initializing this object
     * @param {string}name Only if the name passed in matches the currently active class will we mark this class as complete
     * @return {Promise}
     */
    _sendInitComplete(name) {
        if (name === this.constructor.smID) {
            return this._sendAvailable(name)
                       .then(i => {
                           this._isComplete = true;
                           this.send(this.EVENTS.item(Std.EVENTS.item('init').COMPLETE).STATIC, this);
                           return this;
                       });
        }
        return Promise.resolve(null);
    }
    
    /**
     * Emit an event saying that this object is complete enough to be available
     *
     * @param {string} name The name of the class calling this function. Only the current class should call this function effectively
     * @private
     */
    _sendAvailable(name) {
        if (name === this.constructor.smID && !this._isAvailable) {
            this._isAvailable = true;
            return this.send(this.EVENTS.item(Std.EVENTS.item('available')).STATIC, this);
        }
        return Promise.resolve(null);
    }
    
    get(name) {
        return this._attributes.get(name);
    }
    
    receive(eventName, fn, once = true) {return this.constructor._receive(this, ...arguments);}
    
    send(eventName, ...args) {
        this._Events.emit(eventName, ...args);
        this.constructor.send(eventName, ...args);
        if (this.constructor !== Std) {
            Std.send(eventName, ...args);
        }
        return Promise.resolve(this);
    }
    
    //endregion
}

export default Std;
export {Std};