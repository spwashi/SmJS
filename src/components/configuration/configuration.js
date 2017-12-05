import {errors} from './constants';

/**
 *
 */
export class Configuration {
    /**@type Array<Function> An array of functions that we'd apply to the objects we configure**/
    _queue;
    
    getConfigurationHandlers(owner): Array<Promise | any> {
        const handlers = [];
        
        for (let property in this) {
            if (!this.hasOwnProperty(property)) continue;
            
            const toJSON__length = 'toJSON_'.length;
            
            if (property.substr(0, toJSON__length) === 'toJSON_') continue;
            
            const handler = this[property];
            if (typeof handler !== "function") continue;
            
            const promise = Promise.resolve(handler(owner));
            
            handlers.push(promise);
        }
        
        return handlers;
    }
    
    configure(owner): Promise {
        if (owner === null) owner = {};
        if (typeof owner !== "object") throw new Error(errors.CONFIGURATION__EXPECTED_OBJECT);
        
        const configurationSteps = this.getConfigurationHandlers(owner);
        
        return Promise.all(configurationSteps);
    }
}