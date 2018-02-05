import {errors} from './constants';
import {Configurable} from "./types";
import EventManager from "../event/eventManager";
import Identity, {createIdentityManager} from "../identity/components/identity";
import * as deepmerge from "deepmerge";

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
    const SESSION_EVENT__CONFIG = original.$EVENTS$.instance(EVENT__CONFIG);
    const emitConfiguration     = original._eventManager.createEmitter(SESSION_EVENT__CONFIG);
    
    return {
        ...original,
        ...{
            emitConfig: configIndex => {
                original.emitConfig(...arguments);
                emitConfiguration(...arguments);
            },
            waitFor:    configIndex => original._eventManager.waitForEvent(SESSION_EVENT__CONFIG[configIndex])
        }
    };
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
        this._identity     = Configuration.identityManager.identityFor();
        this.$EVENTS$      = Configuration.$EVENTS$.instance(this._identity);
    }
    
    get config() {return this._config;}
    
    get identity(): Identity {return this._identity;}
    
    emitConfig(configIndex, configValue, owner, configResult, configurationSession: ConfigurationSession) {
        const emitConfig = this.emitConfig = (...args) => {
            const [configIndex, configValue, owner, configResult, configurationSession] = args;
            this._eventManager.logEvent(Configuration.$EVENTS$.instance(EVENT__CONFIG), [...args]);
            this._eventManager.logEvent(Configuration.$EVENTS$.instance(EVENT__CONFIG)[configIndex], [...args]);
            this._eventManager.logEvent(this.$EVENTS$.instance(EVENT__CONFIG), [...args]);
            this._eventManager.logEvent(this.$EVENTS$.instance(EVENT__CONFIG)[configIndex], [...args]);
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
    
    getConfigurationHandlers(owner, configurationObject: {}): Array<Promise | any> {
        const handlingFunctions = this.handlers;
        
        // Proxies the Configuration object to make it easier to define events
        const configurationSession = createConfigurationSession(this);
        
        // An array of the functions we will run to configure this item
        const handlers = [];
        
        for (let handlerIndex in handlingFunctions) {
            if (!handlingFunctions.hasOwnProperty(handlerIndex)) continue;
    
            const configureValue = handlingFunctions[handlerIndex];
    
            if (typeof configureValue !== "function") {
                continue;
            }
    
            const configValue = configurationObject[handlerIndex];
    
            const result = configureValue(configValue, owner, configurationSession);
    
            const emitConfigurationEvent = result => {
                configurationSession.emitConfig(handlerIndex, configValue, owner, result, configurationSession);
                return result;
            };
    
            handlers.push(Promise.resolve(result)
                                 .then(emitConfigurationEvent));
        }
        
        return handlers;
    }
    
    /**
     * Given an object (or whatever, maybe) that refers to the Configuration we intend to use,
     * return an object representative of what that configuration should be to the object it's being applied to
     *
     * @param config
     * @param owner
     * @return {Promise.<T>}
     */
    resolveConfiguration(config, owner: {}): Promise<Object> {
        return Promise.resolve(config);
    }
    
    configure(owner: Configurable): Promise {
        owner = owner || {};
        if (typeof owner !== "object") {
            throw new Error(errors.CONFIGURATION__EXPECTED_OBJECT);
        }
    
        const config = this._config;
    
        return this.resolveConfiguration(config, owner)
                   .then(config => {
                       const configHandlers = this.getConfigurationHandlers(owner, config);
    
                       return Promise.all(configHandlers)
                                     .then(i => config);
                   })
                   .then(config => {
                       // set the owner's configuration to an object that includes what we just used
                       owner[CONFIGURATION] = {...(owner[CONFIGURATION] || {}), ...config};
                       const emitEnd        = this._eventManager.createEmitter(Configuration.$EVENTS$.instance(EVENT__CONFIG).END);
                       emitEnd(owner);
                       return owner;
                   });
    }
}

/**
 * ConfiguredEntities might inherit things -- this is a helper function to resolve inherited items in a standard fashion
 *
 *
 * @param config
 * @param resolveConfiguredEntity
 * @return {Promise.<TResult>}
 */
export const resolveInheritedConfiguration = (config, resolveConfiguredEntity) => {
    config.inherits = Array.isArray(config.inherits) ? config.inherits : [config.inherits];
    const promises  = [];
    
    config.inherits.forEach(inherited_item_id => {
        const resolveEntity         = resolveConfiguredEntity(inherited_item_id);
        const entityConvertedToJSON = resolveEntity.then((entity: Entity) => {
            return JSON.parse(JSON.stringify(entity));
        });
        
        promises.push(entityConvertedToJSON);
    });
    
    return Promise.all(promises)
                  .then(configs => deepmerge.all([...configs, config]));
};

Configuration.identityManager  = createIdentityManager('CONFIGURATION');
Configuration.$EVENTS$         = Configuration.identityManager.component('$EVENTS$');
export const CONFIGURATION_END = Configuration.$EVENTS$.instance(EVENT__CONFIG).END;