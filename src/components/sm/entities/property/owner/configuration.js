import {makePropertyOwner, PropertyOwner} from "./owner";
import PropertyConfig from "../configuration";
import {Property} from "../property";
import {Configuration} from "../../../../configuration";

export interface PropertyOwnerConfig {
    static Property: typeof Property;
    static PropertyConfig: typeof PropertyConfig;
    
    configureProperty(name: string, propertyConfig: {}, owner: PropertyOwner): Promise<Property>;
}

export const makePropertyOwnerConfig = (PropertyOwnerConfig: typeof PropertyOwnerConfig | Configuration | Function) => {
    PropertyOwnerConfig.prototype.configureProperty =
        (originalPropertyName: string, originalPropertyConfig: {}, propertyOwner: PropertyOwner): Promise<Property> => {
            const Property              = PropertyOwnerConfig.Property;
            const PropertyConfig        = PropertyOwnerConfig.PropertyConfig;
            originalPropertyConfig.name = propertyOwner.createPropertyIdentity(originalPropertyName);
            const property              = new Property;
            return (new PropertyConfig(originalPropertyConfig))
                .configure(property)
                .then(property => propertyOwner.addProperty(originalPropertyName, property))
                .then(owner => property);
        }
};