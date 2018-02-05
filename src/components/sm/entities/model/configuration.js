import {
    Configuration,
    CONFIGURATION_END,
    configurationHandlerObject,
    resolveInheritedConfiguration
} from "../../../configuration/configuration";
import {Model} from "./model";
import {SM_ID} from "../../identification";
import {ITEM_CONFIGURED__EVENT} from "./events";
import {modelIdentity} from "./identity";
import {configurePropertyForPropertyOwner} from "../property/owner/configuration";

export default class ModelConfiguration extends Configuration {
    handlers: configurationHandlerObject = {
        name:       (name, model) => model[SM_ID] = modelIdentity.identityFor(name),
        properties: (allPropertiesConfig: {} | any, model: Model) => {
            const promises = Object.entries(allPropertiesConfig)
                                   .map(propertyConfigEntry => {
                                       let [name, propertyConfig, configuringEntity] = [propertyConfigEntry[0], propertyConfigEntry[1], model];
                                       return configurePropertyForPropertyOwner(name,
                                                                                propertyConfig,
                                                                                configuringEntity);
                                   });
            return Promise.all(promises);
        }
    };
    
    constructor() {
        super(...arguments);
        this.listenFor(CONFIGURATION_END,
                       null,
                       (configuredItem: Model) => {
                           Model.eventManager.logEvent(ITEM_CONFIGURED__EVENT, [configuredItem]);
                       });
    }
    
    static getConfiguredItem(item) {
        const configuredItemIdentity = modelIdentity.identityFor(item);
        const event                  = ITEM_CONFIGURED__EVENT.instance(configuredItemIdentity);
        return Model.eventManager.waitForEvent(event);
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        config = config || {};
        if (config.inherits) {
            return resolveInheritedConfiguration(config, ModelConfiguration.getConfiguredItem);
        }
        return Promise.resolve(config);
    }
}