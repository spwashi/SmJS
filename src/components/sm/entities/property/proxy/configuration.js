import {Configuration} from "../../../../configuration/configuration";
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";

export const handlers = {
    roleName: (roleName, property: PropertyAsProxyDescriptor) => property._roleName = roleName,
    identity: (identity, property: PropertyAsProxyDescriptor) => property._identity = identity,
};

class PropertyAsProxyConfig extends Configuration {
    handlers = handlers;
}

export default PropertyAsProxyConfig;