import {Property} from "../property";
import {Configuration} from "../../../../configuration";
import type {PropertyOwnerConfig} from "./types";

export const makePropertyOwnerConfig = (PropertyOwnerConfig: typeof PropertyOwnerConfig | Configuration | Function) => {
    PropertyOwnerConfig.prototype.resolvePropertyConfiguration = PropertyOwnerConfig.prototype.resolvePropertyConfiguration || (config => Promise.resolve(config))
    PropertyOwnerConfig.prototype.configureProperty            =
        function (originalPropertyName: string, originalPropertyConfig: {}, propertyOwner: PropertyOwner): Promise<Property> {
            const Property              = PropertyOwnerConfig.Property;
            const PropertyConfig        = PropertyOwnerConfig.PropertyConfig;
            originalPropertyConfig.name = propertyOwner.identifyProperty(originalPropertyName);
            const configurationSession  = this;
            let configureProperty       = config => {
                const property              = new Property;
                const propertyConfiguration = new PropertyConfig(config, configurationSession);
                return propertyConfiguration.configure(property);
            };
            
            const configurationResolved = configurationSession.resolvePropertyConfiguration(originalPropertyConfig);
            return configurationResolved.then(configureProperty).then(property => {
                propertyOwner.addProperty(originalPropertyName, property);
                return property;
            });
        }
};