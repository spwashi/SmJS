import {Configuration} from "../../../../configuration/configuration";
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";
import {Model} from "../../model/model";

export const handlers = {
    roleName: (roleName, property: PropertyAsProxyDescriptor) => property._roleName = roleName,
    identity: (identity, property: PropertyAsProxyDescriptor) => {
        return Model.init(identity)
                    .then(result => {
                        return property._identity = identity;
                    })
                    .catch(err => console.error(err));
    },
};

class PropertyAsProxyConfig extends Configuration {
    handlers = handlers;
}

export default PropertyAsProxyConfig;