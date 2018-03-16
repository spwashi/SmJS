import {Configuration} from "../../../../configuration/configuration";
import {PropertyAsReferenceDescriptor} from "./propertyAsReference";
import type {ConfigurationSession} from "../../../../configuration/configuration";
import type {SmEntity} from "../../types";
import type {PropertyOwner} from "../owner/owner"
import Identity from "../../../../identity/components/identity";

const logger = console;

export const handlers = {
    identity:        (identity: Identity | string, descriptor: PropertyAsReferenceDescriptor, configurationSession: ConfigurationSession | PropertyAsReferenceConfig) => {
        if (!identity) return null;
        return configurationSession.initSmEntity(identity)
                                   .then(smEntity => {
                                       return descriptor._proxied = smEntity;
                                   })
                                   .catch(err => {
                                       logger.error(err)
                                   });
    },
    hydrationMethod: (hydration: { property: string }, descriptor: PropertyAsReferenceDescriptor, configurationSession: ConfigurationSession | PropertyAsReferenceConfig) => {
        if (!hydration) return null;
        
        if (typeof  hydration !== "object" || typeof hydration.property !== "string") {
            throw new Error("Can only hydrate based on Properties");
        }
        const expectedProperty = hydration.property;
        return configurationSession.waitFor('identity')
                                   .then((result: PropertyOwner) => {
                                       const property = result.properties[expectedProperty];
                                       if (!property) {
                                           throw new Error("Configured SmEntity is missing the properties necessary");
                                       }
            
                                       descriptor._hydration = {property};
                                   });
    }
};

/**
 * Useful when Properties represent some other Configurable Entity
 */
class PropertyAsReferenceConfig extends Configuration {
    handlers = handlers;
    _proxiedSmEntityProto: typeof SmEntity;
    
    get smEntityProto(): typeof SmEntity {
        if (!this._proxiedSmEntityProto) throw new Error("No Property Owner was configured for this PropertyAsReference Configuration");
        return this._proxiedSmEntityProto;
    }
    
    set smEntityProto(Proto: typeof SmEntity) {
        this._proxiedSmEntityProto = Proto;
    }
    
    /**
     * Initialize an SmEntity based on what weve configured
     *
     * @param identity
     * @return {Promise<SmEntity>}
     */
    initSmEntity(identity: Identity | string): Promise<SmEntity> {
        return this.smEntityProto.init(identity);
    }
}

export default PropertyAsReferenceConfig;