import {errors} from './constants';
import type {configurationHandlerObject, Configuration as ConfigurationInterface} from "./types";
import {Configurable, ConfigurationSession} from "./types";
import EventManager, {LIFECYCLE__BEGIN, LIFECYCLE__END, LIFECYCLE__INIT} from "../event/eventManager";
import Identity, {createIdentityManager} from "../identity/components/identity";
import * as deepmerge from "deepmerge";
import type {SmEntity} from "../sm/entities/types";
import {CONFIGURATION} from "./symbols";
import {CONFIGURE__EVENT} from "./events";
import {createConfigurationSession} from "./session";

/**
 *
 */
export class Configuration implements ConfigurationInterface {
    _eventManager: EventManager;
    _config: {};
    _identity: Identity;
    _parent: Configuration;
    handlers: configurationHandlerObject = {};
    
    constructor(config = {}, parent: Configuration = null) {
        if (config && typeof config !== "object") {
            throw new Error("Cannot configure non-objects");
        }
        this._parent       = parent;
        this._eventManager = new EventManager;
        parent && this._eventManager.addParent(parent.eventManager);
        this._config   = config || {};
        this._identity = this._createIdentity(config);
        
        this.$EVENTS$ = Configuration.$EVENTS$.instance(this.identity);
    }
    
    get config() {return this._config;}
    
    get eventManager(): EventManager {return this._eventManager}
    
    get identity(): Identity {return this._identity;}
    
    _createIdentity(config: { name: string | undefined }): Identity {
        const identifier = this._getConfiguredIdentifier(config);
        
        if (typeof this._parent === "object" && this._parent && this._parent.identity) {
            return this._parent.identity.component('..' + identifier);
        }
        return Configuration.identityManager.identityFor('~' + identifier);
    }
    
    _getConfiguredIdentifier(config) {
        return config.name ? `${config.name}` : null;
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        return Promise.resolve(config);
    }
    
    listenFor(eventName, comparison: (expected: Array, actual: Array) => {} | Array, callback): void {
        if (typeof comparison === "function") {
            callback   =
                (...args) => {
                    if (comparison(args)) callback(...args);
                };
            comparison = null;
        }
        
        if (!comparison || Array.isArray(comparison)) {
            this._eventManager.createListener(eventName, comparison, callback)
        }
    }
    
    configure(owner: Configurable): Promise {
        owner = owner || {};
        if (typeof owner !== "object") {
            throw new Error(errors.CONFIGURATION__EXPECTED_OBJECT);
        }
        
        const config = this._config;
        this._emitConfigurationEvent({configIndex: null, configEventName: LIFECYCLE__INIT});
        return this.resolveConfiguration(config, owner)
                   .then(config => {
                       this._emitConfigurationEvent({configIndex: null, configEventName: LIFECYCLE__BEGIN});
                       const configHandlers = this._getConfigurationHandlers(owner, config);
            
                       return Promise.all(configHandlers.map(fn => fn()))
                                     .then(i => config);
                   })
                   .then(config => {
                       // set the owner's configuration to an object that includes what we just used
                       owner[CONFIGURATION] = {...(owner[CONFIGURATION] || {}), ...config};
                       this._emitConfigurationEvent({
                                                        configEventName: LIFECYCLE__END,
                                                        eventResult:     owner,
                                                        doEmitGeneric:   true
                                                    });
                       return owner;
                   });
    }
    
    /**
     * Return an array of functions that, once run, will attempt to configure the "owner" of the ConfigurationSession
     *
     * @param {Configurable}owner
     * @param {{}} configuration
     * @return {Array}
     *
     * @private
     */
    _getConfigurationHandlers(owner: Configurable, configuration: {}): Array<() => {}> {
        const handlingFunctions = this.handlers;
        
        // Proxies the Configuration object to make it easier to define events
        const configurationSession               = createConfigurationSession(this);
        configurationSession.configuredObject    = owner;
        configurationSession.configurationObject = configuration;
        
        // An array of the functions we will run to configure this item
        const handlers = [];
        
        // Iterate through the established "handling" functions and wrap them in a function that
        //  emits the proper events and enforces that the returned value is a Promise
        for (let handlerIndex in handlingFunctions) {
            if (!handlingFunctions.hasOwnProperty(handlerIndex)) {
                continue;
            }
            
            // The function that would configure this index on the object
            const configureValueFn = handlingFunctions[handlerIndex];
            
            if (typeof configureValueFn !== "function") {
                continue;
            }
            
            // The function that we run to emit the proper events in the configuration lifecycle
            const configFn = this._createIndexConfigurationFn(handlerIndex,
                                                              configuration[handlerIndex],
                                                              configureValueFn,
                                                              configurationSession);
            
            handlers.push(configFn);
        }
        
        return handlers;
    }
    
    /**
     * Emit a Configuration event
     *
     * @param configIndex
     * @param configEventName
     *
     * @param eventResult
     * @param configValue
     * @param configurationSession
     * @param doEmitGeneric
     * @private
     */
    _emitConfigurationEvent({configIndex, configEventName, eventResult, configValue, configurationSession, doEmitGeneric}): void {
        
        const eventManager  = this._eventManager;
        const $events$Array = [this.$EVENTS$];
        if (doEmitGeneric) $events$Array.push(Configuration.$EVENTS$);
        
        $events$Array
            .forEach($events$ => {
                const eventConfig    = $events$.instance(CONFIGURE__EVENT);
                let configIndexEvent = configIndex ? eventConfig.instance(configIndex) : eventConfig;
                
                if (configEventName) {
                    configIndexEvent = configIndexEvent.instance(configEventName);
                }
                
                eventManager.emitEvent(configIndexEvent, eventResult)
            });
    }
    
    /**
     * Create the function we will return
     * @param handlerIndex
     * @param configurationValue
     * @param configureValueFn
     * @param configurationSession
     * @return {function()}
     * @private
     */
    _createIndexConfigurationFn(handlerIndex: string,
                                configurationValue: any,
                                configureValueFn: Function,
                                configurationSession: ConfigurationSession & Configuration): () => {} {
        
        const owner = configurationSession.configuredObject;
        
        // Return a function that would actually configure the index on the owner that we intend
        return () => {
            configurationSession._emitConfigurationEvent({
                                                             configIndex:          handlerIndex,
                                                             configEventName:      LIFECYCLE__BEGIN,
                                                             eventResult:          null,
                                                             configValue:          configurationValue,
                                                             configurationSession: configurationSession
                                                         });
            
            const configurationResult  = configureValueFn(configurationValue, owner, configurationSession);
            const resolveConfiguration = Promise.resolve(configurationResult);
            return resolveConfiguration.then(result => {
                configurationSession._emitConfigurationEvent({
                                                                 configIndex:          handlerIndex,
                                                                 configEventName:      LIFECYCLE__END,
                                                                 eventResult:          result,
                                                                 configValue:          configurationValue,
                                                                 configurationSession: configurationSession
                                                             });
                return result;
            });
        };
    }
}

/**
 * ConfiguredEntities might inherit things -- this is a helper function to resolve inherited items in a standard fashion
 *
 *
 * @param config
 * @param SmEntityProto
 * @return {Promise<{>}
 */
export function resolveInheritedConfiguration(config: { inherits: string | Array }, SmEntityProto: typeof SmEntity): Promise<{}> {
    config.inherits = Array.isArray(config.inherits) ? config.inherits : [config.inherits];
    const promises  = [];
    
    config.inherits.forEach(inherited_item_id => {
        const resolveEntity         = SmEntityProto.init(inherited_item_id);
        const entityConvertedToJSON = resolveEntity.then((item: SmEntity) => {
            return JSON.parse(JSON.stringify(item));
        });
        
        promises.push(entityConvertedToJSON);
    });
    
    return Promise.all(promises)
                  .then(configs => deepmerge.all([...configs, config]));
}

Configuration.identityManager  = createIdentityManager('-cfg-');
Configuration.$EVENTS$         = Configuration.identityManager.component('$events$');
export const CONFIGURATION_END = Configuration.$EVENTS$.instance(CONFIGURE__EVENT).instance(LIFECYCLE__END);