interface Configurable {

}

/**
 * An object that we would use to Configure an object
 */
interface Configuration {
    eventManager: EventManager;
    identity: Identity;
    _parent: Configuration;
    config: {};
    handlers: configurationHandlerObject;
    
    /**
     * Listen for an event with the given name
     *
     * @param eventName
     * @param comparison
     * @param callback
     */
    listenFor(eventName: string,
              comparison: (expected: Array, actual: Array) => {} | Array,
              callback: Function): void;
    
    /**
     * Given an object (or whatever, maybe) that refers to the Configuration we intend to use,
     * return an object representative of what that configuration should be to the object it's being applied to
     *
     * @param config
     * @param owner
     * @return {Promise.<T>}
     */
    resolveConfiguration(config, owner: {}): Promise<Object>;
    
    /**
     * Configure an object
     *
     * @param owner
     */
    configure(owner: Configurable): Promise;
    
    /**
     * Create the Identity of the object
     *
     * @param config
     * @return {Identity}
     * @protected
     */
    _createIdentity(config: { name: string | undefined }): Identity;
}

interface ConfigurationSession extends Configuration {
    emitConfig(configIndex: string, configValue: any, owner: Object, configResult: any, configuration: Configuration,),
    
    waitFor(configIndex: string): Promise,
    
    /**
     * An object representing the object that is being configured in this session
     */
    configurationObject: {},
    /**
     * The object being configured in this Session
     */
    configuredObject: {}
}

type configurationHandler = (config_value: any, owner: {}, configuration: ConfigurationSession) => {};

interface configurationHandlerObject {
    [name: string]: configurationHandler
}

type eventName = string | Symbol;

declare class EventManager {
    emittedEventNames() ;
    
    waitingEventNames() ;
    
    addParent(parent: EventManager) ;
    
    /**
     * Say an event happened
     *
     * @param name
     * @param eventArguments
     */
    emitEvent(name: eventName | Identity, eventArguments: Array) ;
    
    /**
     * Create a promise that resolves with the arguments of the first event that matches what we've specified
     *
     * @param name
     * @param includePast
     * @param eventArguments
     * @return {Promise}
     */
    waitForEvent(name: eventName | Identity, includePast, eventArguments: Array): Promise<Array> ;
    /**
     * Create a function which, when called, notifies this eventManager that the event has been called
     *
     * @param name
     * @param eventArguments
     * @return {function(...[*])}
     */
    createEmitter(name: eventName | Identity, eventArguments: Array): (...args: any) => {} ;
    
    /**
     * Register a function that waits for an event to be called with specified arguments.
     *
     * @param name
     * @param eventArguments
     * @param callback
     */
    createListener(name: eventName | Identity, eventArguments: Array, callback) ;
}

type identifier = string;

declare interface Identifier {
    static identify(name: string): Identity;
}

declare interface Identifiable {
    +IDENTITY: Identity
}

declare type smEntityEventConfig = { CONFIG_END: Identity | undefined };

declare interface IdentityNode {
    instance(identifier: identifier | Identity): IdentityNode;
    
    component(identifier: identifier | Identity): IdentityNode;
}

declare interface IdentityManager {
    static resolve(identity: identifier): Identifiable;
}

class Identity implements IdentityNode {
    _identifier: identifier;
    _identityManager: IdentityManager;
    _parent: Identity;
    
    get identifier(): identifier ;
    
    component(name: identifier | Identity): Identity ;
    
    identifiedInstance(name: identifier | Identity): Identity;
    
    instance(name: identifier | Identity): Identity;
    
    _setParent(parent: Identity) ;
    
    _setIdentityManager(identityManager: IdentityManager) ;
    
    toJSON(): Object;
    
    toString(): string;
}

class PropertyMeta {}

declare module "spwashi-sm" {
    declare type SmEntity :Identifiable & Identifier = {
        eventManager: EventManager,
        events: smEntityEventConfig,
        identify(name: string): Identity,
        init(name: string | Identity): Promise<SmEntity>
    }
    
    declare type PropertyOwner:SmEntity = {
        properties: Object<string, Property>,
        addProperty(propertyName: string, property: Property): PropertyOwner,
        propertyMeta: PropertyMeta,
        identifyProperty(propertyName: string): Identity
    }
    
    declare type Model:SmEntity = {}
    declare type Entity:SmEntity = {}
    declare type Property:SmEntity = {}
    
    declare class Entity implements SmEntity, Configurable, PropertyOwner {}
    
    declare class Property implements SmEntity, Configurable {
    
    }
    
    declare class Model implements SmEntity {}
    
    declare export type Sm = {
        Model: typeof Model,
        Entity: typeof Entity,
        Property: typeof Property
    }
}