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
        function (originalPropertyName: string, originalPropertyConfig: {}, propertyOwner: PropertyOwner): Promise<Property> {
            const Property              = PropertyOwnerConfig.Property;
            const PropertyConfig        = PropertyOwnerConfig.PropertyConfig;
            originalPropertyConfig.name = propertyOwner.identifyProperty(originalPropertyName);
            const configurationSession  = this;
            let configureProperty       = () => {
                const property              = new Property;
                const propertyConfiguration = new PropertyConfig(originalPropertyConfig,
                                                                 configurationSession);
                return propertyConfiguration.configure(property);
            };
            return configureProperty().then(property => {
                propertyOwner.addProperty(originalPropertyName, property);
                return property;
            });
        }
};