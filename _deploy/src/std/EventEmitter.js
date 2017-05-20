import events from "events";
import {SymbolStore} from "./symbols/SymbolStore";
const EVENTS = Symbol('EVENTS');
export {EVENTS};
class EventDescriptor {
    constructor(emitter, eventName, eventFamily) {
        this._emitter     = emitter;
        this._eventName   = eventName;
        this._eventFamily = eventFamily;
    }
    
    get eventName() {return this._eventName;}
    
    get eventFamily() {return this._eventFamily;}
    
    get emitter() {return this._emitter;}
}
/**
 * @class EventEmitter
 * @extends events.EventEmitter
 */
export default class EventEmitter extends events.EventEmitter {
    constructor(emitter) {
        super();
        this._emitter = emitter;
    }
    
    on(event_name, ...args) {
        if (event_name instanceof SymbolStore) event_name = event_name.Symbol;
        return super.on(event_name, ...args);
    }
    
    /**
     * Emit an event
     *
     * @param {string|Symbol|SymbolStore} event_name The event identifier that we are emitting
     * @param args
     */
    emit(event_name, ...args) {
        let family;
        if (event_name instanceof SymbolStore) {
            
            // If this symbol belongs to a family of symbols, also emit those
            family = event_name.family;
            family.forEach(symbol => this.emit(symbol, ...args));
            // We emit this symbol last
            event_name = event_name.Symbol;
        }
        const event = new EventDescriptor(this._emitter || null,
                                          event_name,
                                          family);
        super.emit(event_name, event, ...args);
    }
}
EventEmitter.EVENTS = EVENTS;