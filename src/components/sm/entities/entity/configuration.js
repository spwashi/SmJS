import {
    Configuration,
    CONFIGURATION_END,
    configurationHandlerObject,
    resolveInheritedConfiguration
} from "../../../configuration/configuration";
import {Entity} from "./entity";
import {SM_ID} from "../../identification";
import {makePropertyOwnerConfig} from "../property/owner/configuration";
import {EntityProperty} from "./property/property";
import {EntityPropertyConfig} from "./property/configuration";
import type {PropertyOwnerConfig} from "../property/owner/configuration";
import type {ConfigurationSession} from "../../../configuration/configuration";

const handlers = {
    name:       (name, entity) => entity[SM_ID] = Entity.identify(name),
    properties: (allPropertiesConfig: {} | any, entity: Entity, configuration: ConfigurationSession & EntityConfiguration) => {
        const entries  = Object.entries(allPropertiesConfig);
        const promises = entries.map(([name, propertyConfig]) => configuration.configureProperty(name, propertyConfig, entity));
        return Promise.all(promises);
    }
};
export default class EntityConfiguration extends Configuration implements PropertyOwnerConfig {
    handlers: configurationHandlerObject = handlers;
    static Property                      = EntityProperty;
    static PropertyConfig                = EntityPropertyConfig;
    
    constructor() {
        super(...arguments);
        this.listenFor(CONFIGURATION_END,
                       null,
                       (configuredItem: Entity) => {
                           const ENTITY_CONFIGURED__EVENT = Entity.events.CONFIG_END;
                           const eventArguments           = [configuredItem];
                           Entity.eventManager.logEvent(ENTITY_CONFIGURED__EVENT, eventArguments);
                       });
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        config = config || {};
        return config.inherits ? resolveInheritedConfiguration(config, Entity) : Promise.resolve(config);
    }
}

makePropertyOwnerConfig(EntityConfiguration);