import {
    Configuration,
    CONFIGURATION_END,
    configurationHandlerObject,
    resolveInheritedConfiguration
} from "../../../configuration/configuration";
import {Entity} from "./entity";
import {SM_ID} from "../../identification";
import {ITEM_CONFIGURED__EVENT} from "./events";
import {entityIdentity} from "./identity";
import {configurePropertyForPropertyOwner} from "../property/owner/configuration";
import {EntityProperty} from "./property/property";
import {EntityPropertyConfig} from "./property/configuration";

const handlers = {
    name:       (name, entity) => entity[SM_ID] = Entity.identify(name),
    properties: (allPropertiesConfig: {} | any, entity: Entity) => {
        const promises = Object.entries(allPropertiesConfig)
                               .map(propertyConfigEntry => {
                                   let [name, propertyConfig, configuringEntity] = [propertyConfigEntry[0], propertyConfigEntry[1], entity];
                                   return configurePropertyForPropertyOwner(name,
                                                                            propertyConfig,
                                                                            configuringEntity,
                                                                            EntityProperty,
                                                                            EntityPropertyConfig);
                               });
        return Promise.all(promises);
    }
};
export default class EntityConfiguration extends Configuration {
    handlers: configurationHandlerObject = handlers;
    
    constructor() {
        super(...arguments);
        this.listenFor(CONFIGURATION_END,
                       null,
                       (configuredItem: Entity) => {
                           Entity.eventManager.logEvent(ITEM_CONFIGURED__EVENT, [configuredItem]);
                       });
    }
    
    static getConfiguredItem(item) {
        const configuredItemIdentity = Entity.identify(item);
        const event                  = ITEM_CONFIGURED__EVENT.instance(configuredItemIdentity);
        return Entity.eventManager.waitForEvent(event);
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        config = config || {};
        if (config.inherits) {
            return resolveInheritedConfiguration(config, EntityConfiguration.getConfiguredItem);
        }
        return Promise.resolve(config);
    }
}