import {Configuration} from "../../../../configuration/configuration";
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";
import {PropertyOwner} from "../owner"
import type {ConfigurationSession} from "../../../../configuration/configuration";
import type {SmEntity} from "../../types";

const logger = console;

export const handlers = {
    roleName: (roleName, property: PropertyAsProxyDescriptor, configurationSession: ConfigurationSession) => property._roleName = roleName,
    identity: (identity, property: PropertyAsProxyDescriptor, configurationSession: ConfigurationSession | PropertyAsProxyConfig) => {
        const propertyOwningSmEntity = configurationSession.propertyOwnerProto;
        
        const whenSmEntityExists = propertyOwningSmEntity.init(identity);
        return whenSmEntityExists.then(result => {
                                     return property._identity = identity;
                                 })
                                 .catch(err => {
                                     logger.error(err)
                                 });
    },
};

class PropertyAsProxyConfig extends Configuration {
    handlers = handlers;
    _propertyOwnerPrototype: typeof PropertyOwner & SmEntity;
    
    get propertyOwnerProto(): typeof PropertyOwner & SmEntity {
        if (!this._propertyOwnerPrototype) throw new Error("No Property Owner was configured for this PropertyAsProxy Configuration");
        return this._propertyOwnerPrototype;
    }
    
    set propertyOwnerProto(Proto: typeof PropertyOwner & SmEntity) {
        this._propertyOwnerPrototype = Proto;
    }
    
}

export default PropertyAsProxyConfig;