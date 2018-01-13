import {errors} from './constants';
import {Configurable} from "./types";

export type configFunc = (config_value: any, owner: {}, configuration: Configuration) => {};
export const CONFIGURATION = Symbol('configuration for the object');

export interface configurationHandlerObject {
    [name: string]: configFunc
}

const ALL_CONFIGURED = Symbol('All items from this configuration have been configured');

interface promiseResolutionObject {
    resolve: () => {},
    reject: () => {},
}

/**
 *
 */
export class Configuration {
    handlers: configurationHandlerObject                                 = {};
    // An object of arrays of promises (arranged by configuration name)
    _waitingPromises: { [name: string]: Array<promiseResolutionObject> } = {};
    _config: {};
    
    constructor(config = {}) {
        if (config && typeof config !== "object") {
            throw new Error("Cannot configure non-objects");
        }
        this._waitingPromises = {};
        this._config          = config || {};
    }
    
    get config() {
        return this._config;
    }
    
    _dispatchConfigurationEnd(item: Symbol | string | null) {
        const promises: Array = this._waitingPromises[item];
        if (!Array.isArray(promises)) return;
        
        promises.forEach((resolutionObject: promiseResolutionObject) => {
            resolutionObject.resolve()
        });
    }
    
    /**
     * Wait for an item to finish configuring before resolving a Promise.
     * If there are no argments passed, assume we want everything configured
     *
     * @param item
     * @return {Promise}
     */
    whenDoneConfiguring(item: Symbol | string | null = ALL_CONFIGURED) {
        let resolve, reject;
        
        return new Promise((resolvePromise, rejectPromise) => {
            let called = false;
            
            // only run these functions once
            [resolve, reject] = [
                () => (!called) && (called = true) && resolvePromise(),
                () => (!called) && (called = true) && rejectPromise()
            ];
            
            this._waitingPromises[item] = this._waitingPromises[item] || [];
            this._waitingPromises[item].push({resolve, reject})
        })
    }
    
    getConfigurationHandlers(owner): Array<Promise | any> {
        const handlers = [];
    
        const handlingFunctions = this.handlers;
        for (let handlerIndex in handlingFunctions) {
            if (!handlingFunctions.hasOwnProperty(handlerIndex)) continue;
        
            const configure_item = handlingFunctions[handlerIndex];
            if (typeof configure_item !== "function") continue;
        
            const config_value = this._config[handlerIndex];
        
            const result  = configure_item(config_value, owner, this);
            const promise = Promise.resolve(result)
                                   .then(result => {
                                       this._dispatchConfigurationEnd(handlerIndex);
                                       return result;
                                   });
            
            handlers.push(promise);
        }
        
        return handlers;
    }
    
    configure(owner: Configurable): Promise {
        if (owner === null) owner = {};
        if (typeof owner !== "object") throw new Error(errors.CONFIGURATION__EXPECTED_OBJECT);
        
        const configurationSteps = this.getConfigurationHandlers(owner);
        owner[CONFIGURATION]     = this._config;
        return Promise.all(configurationSteps).then(i => owner);
    }
}