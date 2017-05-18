/**
 * Created by Sam Washington on 5/7/17.
 */
import {EventEmitter} from "events";
import SymbolRegistrator from "../symbols/SymbolRegistrator";
import {SYMBOL, symbolModifiers} from "../symbols/index";
import {_to_string} from "../util/index";

/**
 * @class _EmittedEventsMap
 * @extends Map
 */
class _EmittedEventsMap extends Map {
}

export default class StdEventEmitter extends EventEmitter {
    constructor(_reference) {
        super();
        this._reference      = _reference || null;
        this._emitted_events = new _EmittedEventsMap;
    }
    
    static _initClass() {
        if (this._is_init) return null;
        this._is_init = true;
    }
    
    emit(event_name, ...args) {
        const e = typeof event === 'symbol' ? event.toString() : event;
        this._emitted_events.set(_to_string(event_name), [...args]);
        this._reference && event_name !== '*' && this.emit('*', event_name, this._reference, ...args);
        return super.emit(...arguments);
    }
    
    emitOnce(event_name, ...args) {
        let _string = _to_string(event_name);
        if (this._emitted_events.has(_string)) return this;
        this._emitted_events.set(_string, [...args]);
        return this.emit(...arguments)
    }
    
    bindEvent(event_name, Handler) {
        if (!(Handler instanceof StdEventEmitter)) throw  new Error('Handler to bind events must be StdEventEmitter');
        if (Array.isArray(event_name)) {
            event_name.forEach(event_name => this.bindEvent(event_name, Handler));
            return this;
        }
        let emit_Handler = (...res) => Handler.emit(event_name, ...res);
        
        const _string = _to_string(event_name);
        if (this._emitted_events.has(_string)) {
            emit_Handler(...this._emitted_events.get(_string));
        }
        
        this.on(event_name, emit_Handler);
        return this;
    }
    
    when(event_name, indicator = null) {
        let events = Array.isArray(event_name) ? event_name : false;
        if (events) {
            let _P = events.map(_name => this.when(_name, indicator));
            return Promise.all(_P);
        }
        
        let _has_resolved       = false;
        let take_promise_action = (action) => {
            return (..._result) => {
                if (_has_resolved) return;
                _has_resolved = true;
                return action(..._result);
            };
        };
        
        if (indicator instanceof Function || typeof indicator === "function") {
            let _original_tpa   = take_promise_action;
            take_promise_action = (action) => {
                let take_promise_action = _original_tpa(action);
                
                return (..._result) => {
                    if (indicator(..._result) === true) {
                        return take_promise_action(..._result);
                    }
                }
            }
        }
        
        return new Promise((resolve, reject) => {
            resolve = take_promise_action(resolve);
            reject  = take_promise_action(reject);
            
            const _string = _to_string(event_name);
            if (this._emitted_events.has(_string)) {
                let _event_args = this._emitted_events.get(_string);
                if (event_name === require('./index').$Events$.init.self[SYMBOL]) {
                    // console.log('YUP', this._reference);
                }
                _event_args = Array.isArray(_event_args) ? _event_args : [];
                return resolve(..._event_args);
            }
            this.once(event_name, resolve);
            let error_event;
            
            if (typeof event_name === 'symbol') {
                if (SymbolRegistrator[event_name]) error_event = SymbolRegistrator[event_name][symbolModifiers.ERROR];
                else
                    throw new Error(event_name.toString() + ' has not been registered')
            } else {
                error_event = `error[${event_name}]`;
            }
            this.once(error_event, reject);
        });
    }
}

StdEventEmitter.prototype._maxListeners = 100;
StdEventEmitter.Events                  = new StdEventEmitter;

StdEventEmitter.Events.emit('fn');