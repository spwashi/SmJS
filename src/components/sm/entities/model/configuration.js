import {Configuration, CONFIGURATION, configurationHandlerObject, ConfigurationSession, EVENT__CONFIG} from "../../../configuration/configuration";
import PropertyConfig from "./property/configuration";
import {Property} from "./property/property";
import {Meta as PropertyMeta} from "./property/meta";
import {Model} from "./model";
import {SM_ID} from "../../identification";
import {CONFIGURED_MODEL} from "./events";
import identity, {modelIdentity} from "./identity";
import * as deepmerge from "deepmerge";

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
        name:       (name, model) => model[SM_ID] = modelIdentity.create(name),
        properties: (config_value: {} | any, owner: Model, configuration: ConfigurationSession) => {
            const model             = owner;
            const properties_config = config_value;
            const propertyMeta      = model.propertyMeta;
    
            const promises = Object.entries(properties_config)
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
    };
    
    constructor() {
        super(...arguments);
        this.listenFor(Configuration.$EVENTS$.item(EVENT__CONFIG).END,
                       null,
                       (configuredItem: Model) => {
                           Model.eventManager.logEvent(CONFIGURED_MODEL, [configuredItem]);
                       });
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        config = config || {};
        if (config.inherits) {
            
            config.inherits = Array.isArray(config.inherits) ? config.inherits : [config.inherits];
            let promises    = [];
            
            config.inherits.forEach(inheriting => {
                const event = CONFIGURED_MODEL.item(modelIdentity.create(inheriting));
                
                const modelLoaded = Model.eventManager
                                         .waitForEvent(event);
                
                const convertToJSON = modelLoaded.then((model: Model) => JSON.parse(JSON.stringify(model)));
                
                promises.push(convertToJSON);
            });
            
            return Promise.all(promises)
                          .then(configs => deepmerge.all([...configs, config]));
        }
        return Promise.resolve(config);
    }
}