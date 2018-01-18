import {errors} from './constants';
import {Configurable} from "./types";
import EventManager from "../event/eventManager";
import Identity, {createIdentityManager} from "../identity/components/identity";

export const CONFIGURATION = Symbol('configuration for the object');

export interface ConfigurationSession {
    emitConfig: (configIndex: string, configValue: any, owner: Object, configResult: any, configuration: Configuration,) => {},
    
    waitFor: (configIndex: string) => {}
    
}

export type configurationHandler = (config_value: any, owner: {}, configuration: ConfigurationSession) => {};

export interface configurationHandlerObject {
    [name: string]: configurationHandler
}

export const EVENT__CONFIG = ('CONFIGURE');

const createConfigurationSession = (original: Configuration) => {
    const SESSION_EVENT__CONFIG = original.$EVENTS$.item(EVENT__CONFIG);
    const emitConfiguration     = original._eventManager.createEmitter(SESSION_EVENT__CONFIG);
    
    return new Proxy(original, {
        get: (target, name) => {
            const proxy = {
                emitConfig: function (configIndex: string) {
                    target.emitConfig(...arguments);
                    emitConfiguration(...arguments);
                },
                
                waitFor: configIndex => {
                    return target._eventManager.waitForEvent(SESSION_EVENT__CONFIG[configIndex]);
                }
            };
            return proxy[name] || target[name];
        }
    });
};

/**
 *
 */
export class Configuration {
    _eventManager: EventManager;
    _config: {};
    _identity: Identity;
    handlers: configurationHandlerObject = {};
    
    constructor(config = {}) {
        if (config && typeof config !== "object") {
            throw new Error("Cannot configure non-objects");
        }
        this._eventManager = new EventManager;
        this._config       = config || {};
        this._identity     = Configuration.identityManager.create();
        this.$EVENTS$      = Configuration.$EVENTS$.item(this._identity);
    }
    
    get config() {return this._config;}
    
    get identity(): Identity {return this._identity;}
    
    emitConfig(configIndex, configValue, owner, configResult, configurationSession: ConfigurationSession) {
        const emitConfig = this.emitConfig = (...args) => {
            const [configIndex, configValue, owner, configResult, configurationSession] = args;
            this._eventManager.logEvent(Configuration.$EVENTS$.item(EVENT__CONFIG), [...args]);
            this._eventManager.logEvent(Configuration.$EVENTS$.item(EVENT__CONFIG)[configIndex], [...args]);
            this._eventManager.logEvent(this.$EVENTS$.item(EVENT__CONFIG), [...args]);
            this._eventManager.logEvent(this.$EVENTS$.item(EVENT__CONFIG)[configIndex], [...args]);
        };
        emitConfig(...arguments);
    }
    
    listenFor(eventName, comparison: (expected: Array, actual: Array) => {} | Array, callback) {
        if (typeof comparison === "function") {
            this._eventManager
                .createListener(eventName,
                                null,
                                (...args) => {
                                    if (comparison(args)) callback(...args);
                                })
        } else if (!comparison || Array.isArray(comparison)) {
            this._eventManager
                .createListener(eventName,
                                comparison,
                                callback)
        }
    }
    
    getConfigurationHandlers(handlingFunctions: {}, owner): Array<Promise | any> {
        const handlers             = [];
        const configurationSession = createConfigurationSession(this);
        for (let handlerIndex in handlingFunctions) {
            if (!handlingFunctions.hasOwnProperty(handlerIndex)) continue;
    
            const configureValue = handlingFunctions[handlerIndex];
            if (typeof configureValue !== "function") continue;
    
            const configValue = this._config[handlerIndex];
            const result      = configureValue(configValue, owner, configurationSession);
            const promise     = Promise.resolve(result)
                                       .then(result => {
                                           configurationSession.emitConfig(handlerIndex, configValue, owner, result, configurationSession);
                                           return result;
                                       });
            
            handlers.push(promise);
        }
        
        return handlers;
    }
    
    configure(owner: Configurable): Promise {
        if (owner === null) owner = {};
        if (typeof owner !== "object") throw new Error(errors.CONFIGURATION__EXPECTED_OBJECT);
    
        const configurationSteps = this.getConfigurationHandlers(this.handlers, owner);
        const emitEnd            = this._eventManager.createEmitter(Configuration.$EVENTS$.item(EVENT__CONFIG).END);
        owner[CONFIGURATION]     = this._config;
        return Promise.all(configurationSteps).then(i => (emitEnd(owner), owner));
    }
}

Configuration.identityManager = createIdentityManager('CONFIGURATION');
Configuration.$EVENTS$        = Configuration.identityManager.component('$EVENTS$');