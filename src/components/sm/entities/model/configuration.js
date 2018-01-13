import {Configuration, CONFIGURATION, configurationHandlerObject} from "../../../configuration/configuration";
import PropertyConfig from "./property/configuration";
import {Property} from "./property/property";
import {Meta as PropertyMeta} from "./property/meta";
import {Model} from "./model";
import {configureName} from "../../identification";

const _incorporatePropertyIntoMeta = (property: Property, propertyMeta: PropertyMeta) => {
    const config = property[CONFIGURATION];
    if (config.primary) propertyMeta.addPropertiesToPrimaryKey(property);
    
    let unique = config.unique;
    if (typeof unique !== 'string') unique = !!unique;
    if (unique === true) unique = 'unique_key';
    
    if (unique) propertyMeta.addPropertiesToUniqueKey(unique, property);
    return property;
};

export default class ModelConfiguration extends Configuration {
    handlers: configurationHandlerObject = {
        name:       configureName.ofType('Model'),
        properties: (config_value: {} | any, owner: Model, configuration: Configuration) => {
            const model             = owner;
            const properties_config = config_value;
            
            const propertyMeta = model.propertyMeta;
            const promises     = Object.entries(properties_config)
                                       .map(([property_name, property_config]) => {
                
                                           property_config.name = model.createPropertyName(property_name);
                
                                           const propertyConfig = new PropertyConfig(property_config);
                                           return propertyConfig.configure(new Property)
                                                                .then(property => {
                                                                    model._properties[property_name] = property;
                                                                    _incorporatePropertyIntoMeta(property, propertyMeta);
                                                                    return property;
                                                                });
                                       });
            return Promise.all(promises);
        }
    }
}