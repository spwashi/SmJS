import type {ConfigurationSession as ConfigurationSessionInterface, Configuration, Configurable} from "./types";
import {CONFIGURE__EVENT} from "./events";
import Identity from "../identity/components/identity";
import {LIFECYCLE__END} from "../event/eventManager";

export function createConfigurationSession(original: Configuration): ConfigurationSessionInterface {
    
    /**
     * Represents One singular object being configured
     * (assumed to be by a Configuration object that might be responsible for
     *      configuring multiple objects)
     *
     * @type {{new(): ConfigurationSession}}
     */
    const ConfigurationSession = class extends original.constructor implements Configuration, ConfigurationSessionInterface {
        _configuredObject: Object;
        _configurationObject: Object;
        
        constructor() {
            super({}, original);
            
            // The configuration session inherit the methods and properties
            for (let prop in original) {
                if (!original.hasOwnProperty(prop)) continue;
                if (this.hasOwnProperty(prop)) continue;
                
                this[prop] = original[prop];
            }
        }
        
        get configuredObject(): Configurable {return this._configuredObject}
        
        /**
         * Associate this ConfigurationSession with the object it is configuring
         *
         * @param configuredObject
         */
        set configuredObject(configuredObject: Configurable) {
            if (!this._configuredObject) this._configuredObject = configuredObject;
            else console.error("Cannot declare a configured object after it has already been set");
        }
        
        get configurationObject(): {} { return this._configurationObject }
        
        /**
         * Associate this ConfigurationSession with the object it is configuring
         *
         * @param configurationObject
         */
        set configurationObject(configurationObject: {}) {
            if (!this._configurationObject) this._configurationObject = configurationObject;
            else console.error("Cannot declare a configuration object after it has already been set");
        }
        
        _createIdentity(config: { name: string | undefined }): Identity {
            return original.identity.identifiedInstance(Math.random().toString(36).substr(4, 6));
        }
        
        waitFor(configIndex) {
            const configEvent = this.$EVENTS$
                                    .instance(CONFIGURE__EVENT)
                                    .instance(configIndex)
                                    .instance(LIFECYCLE__END);
            console.log('WAIT FOR -- ' + configEvent);
            return this._eventManager.waitForEvent(configEvent)
        }
    };
    
    return new ConfigurationSession;
}