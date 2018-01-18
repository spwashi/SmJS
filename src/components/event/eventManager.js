import {arraysEqual} from "../../util/index";
import Identity from "../identity/components/identity";

interface promiseResolutionObject {
    resolve: () => {},
    reject: () => {},
    eventArguments: Array,
    after: number
}

type eventName = string | Symbol;

/**
 * An object that we can use to wait for or keep track of events.
 */
export default class EventManager {
    /** @private */
    _waitingPromises: { [name: string]: Array<promiseResolutionObject> } = {};
    /** @private */
    _emittedEventNames: { [name: string]: Array<{ eventArguments: Array }> };
    /** @private */
    _listeners: { [name: string]: Array<() => {}> };
    
    constructor() {
        this._emittedEventNames = {};
        this._listeners         = {};
        this._waitingPromises   = {};
    }
    
    /**
     * Say an event happened
     *
     * @param name
     * @param eventArguments
     */
    logEvent(name: eventName | Identity, eventArguments: Array = []) {
        if (name instanceof Identity) name = name.identifier;
        // console.log(`EMITTING: ${name}`);
        this._emittedEventNames[name] = this._emittedEventNames[name] || [];
        this._emittedEventNames[name].push({eventArguments});
        
        this._resolveName(name, true);
        (this._listeners[name] || []).forEach(callback => {
            callback(...eventArguments)
        });
    }
    
    /**
     * Create a promise that resolves with the arguments of the first event that matches what we've specified
     *
     * @param name
     * @param includePast
     * @param eventArguments
     * @return {Promise}
     */
    waitForEvent(name: eventName | Identity, includePast = false, eventArguments: Array = []): Promise<Array> {
        if (name instanceof Identity) name = name.identifier;
        
        const waitingPromises = this._waitingPromises;
        
        return new Promise((resolvePromise, rejectPromise) => {
            
            let completed                                   = false;
            const resolutionObject: promiseResolutionObject = {
                      resolve: (...args) => {(!completed) && (completed = true) && resolvePromise(...args)},
                      reject:  (...args) => {(!completed) && (completed = true) && rejectPromise(...args)},
                
                      eventArguments,
                
                      // If we want to include events that we have already emitted, this is the index that we want to start accepting emitted events after
                      after: includePast ? 0 : (this._emittedEventNames[name] || []).length,
                  }
            ;
            
            waitingPromises[name] = waitingPromises[name] || [];
            waitingPromises[name].push(resolutionObject);
            // console.log(`ABOUT TO CHECK - ${name} WITH ${JSON.stringify(eventArguments)}`);
            
            if (includePast) this._resolveName(name);
        })
    }
    
    /**
     * Create a function which, when called, notifies this eventManager that the event has been called
     *
     * @param name
     * @param eventArguments
     * @return {function(...[*])}
     */
    createEmitter(name: eventName | Identity, eventArguments: Array = []): (...args: any) => {} {
        if (name instanceof Identity) name = name.identifier;
        return (...args) => {
            const eventArgs = [...eventArguments, ...args];
            this.logEvent(name, eventArgs)
        }
    }
    
    /**
     * Register a function that waits for an event to be called with specified arguments.
     *
     * @param name
     * @param eventArguments
     * @param callback
     */
    createListener(name: eventName | Identity, eventArguments: Array = [], callback = () => {}) {
        if (name instanceof Identity) name = name.identifier;
        this._listeners[name] = this._listeners[name] || [];
        const listener        = (...args) => {
            eventArguments = eventArguments || [];
            if (!eventArguments.length || arraysEqual(eventArguments, args)) {
                const eventArgs = [...eventArguments, ...args];
                callback(...eventArgs);
            }
        };
        
        this._listeners[name].push(listener);
    }
    
    /**
     * Execute all waiting promises that can be satisfied with a given event name
     *
     * @param name
     * @private
     */
    _resolveName(name: eventName | Identity) {
        if (name instanceof Identity) name = name.identifier;
        const promiseObjects: Array<promiseResolutionObject> = this._waitingPromises[name];
        if (!promiseObjects) {
            return;
        }
        
        // console.log('TRYING TO RESOLVE: ' + name);
        for (let i = 0; i < promiseObjects.length; i++) {
            const resolutionObject = promiseObjects[i];
            const eventArguments   = resolutionObject.eventArguments;
            if (resolutionObject.completed) continue;
            
            // console.log(`USING ${JSON.stringify(eventArguments)}`);
            
            const matchingEvent = this._checkEvent(name, eventArguments, resolutionObject.after);
            if (!matchingEvent) continue;
            
            // console.log(`RESOLVING - ${name} WITH ${JSON.stringify(eventArguments)}`);
            resolutionObject.completed = true;
            resolutionObject.resolve(matchingEvent.eventArguments);
        }
        
    }
    
    /**
     * Check to see if an event has been emitted (with given eventArguments) after a certain index
     * @param name
     * @param eventArguments
     * @param after
     * @return {boolean}
     * @private
     */
    _checkEvent(name, eventArguments, after: number = 0) {
        if (name instanceof Identity) name = name.identifier;
        if (!this._emittedEventNames[name]) {
            return false;
        }
        
        if (Array.isArray(eventArguments)) {
            // loop variable
            let actual;
            
            // an array of the values we need to know about
            const expectedNeeds = eventArguments;
            const eventLogs     = this._emittedEventNames[name];
            
            //iterate through emitted events with the requested event name and return true if an event matches
            for (let i = 0; i < eventLogs.length; i++) {
                
                if (i < after) {
                    // console.log('skipping - ' + i);
                    continue;
                }
                
                // an array of the indices in this eventLog entry (of eventArguments for that event)
                const resolutionObj = eventLogs[i];
                actual              = resolutionObj.eventArguments;
                
                if (expectedNeeds.length > actual.length) {
                    // we expected the actual to have more entries
                    continue;
                }
                if (!expectedNeeds.length || arraysEqual(expectedNeeds, actual)) {
                    return resolutionObj;
                }
                // console.log('--', actual, expectedNeeds);
            }
            return false;
        }
        
        return true;
    }
}