import EventManager from "../event/eventManager";
import Identity from "../identity/components/identity";

export interface Configurable {

}

/**
 * An object that we would use to Configure an object
 */
export interface Configuration {
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

export interface ConfigurationSession extends Configuration {
    emitConfig: (configIndex: string, configValue: any, owner: Object, configResult: any, configuration: Configuration,) => {},
    waitFor: (configIndex: string) => {},
    /**
     * An object representing the object that is being configured in this session
     */
    configurationObject: {},
    /**
     * The object being configured in this Session
     */
    configuredObject: {}
}

export type configurationHandler = (config_value: any, owner: {}, configuration: ConfigurationSession) => {};

export interface configurationHandlerObject {
    [name: string]: configurationHandler
}