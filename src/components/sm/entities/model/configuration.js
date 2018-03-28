import {
    Configuration,
    CONFIGURATION_END,
    resolveInheritedConfiguration
} from "../../../configuration/configuration";
import {Model} from "./model";
import {SM_ID} from "../../identification";
import {makePropertyOwnerConfig} from "../property/owner/configuration";
import {ModelProperty} from "./property/property";
import {ModelPropertyConfig} from "./property/configuration";
import {mappedModelRoleObject, ModelRole} from "./role";
import type {PropertyOwnerConfig} from "../property/owner/configuration";
import type {configurationHandlerObject, ConfigurationSession} from "../../../configuration/types";

export const handlers = {
    name:       (name, model) => model[SM_ID] = Model.identify(name),
    properties: (allPropertiesConfig: {} | any, model: Model, configuration: ConfigurationSession & ModelConfiguration) => {
        const entries  = Object.entries(allPropertiesConfig || {});
        const promises = entries.map(([name, propertyConfig]) => {
            return configuration.configureProperty(name, propertyConfig, model);
        });
        return Promise.all(promises);
    },
    map:        (mapConfig, model: Model) => {
        if (typeof mapConfig === "undefined") return null;
        
        if (typeof mapConfig !== "object") {
            throw new Error(`Expected map property to be an object -- ${typeof mapConfig} -- provided`);
        }
        
        const expectedModelRoles                                      = {};
        const mappedModelRoles: Object<string, mappedModelRoleObject> = {};
        for (const mapModel_property_name in mapConfig) {
            if (!mapConfig.hasOwnProperty(mapModel_property_name)) {
                continue;
            }
            
            const mapped_property = mapConfig[mapModel_property_name];
            if (typeof mapped_property !== 'string') {
                throw new Error("Expected the mapped property to be a string");
            }
            
            const model_role_matches = /{([a-zA-Z]+)}([a-zA-Z]+)/.exec(mapped_property) || [];
            const model_role         = model_role_matches[1];
            const model_propertyName = model_role_matches[2];
            
            if (!model_role || !model_propertyName) {
                throw new Error("Improper index -- " + JSON.stringify(model_role_matches));
            }
            
            const modelRole                          = new ModelRole(model_role, [model_propertyName]);
            expectedModelRoles[model_role]           = modelRole;
            mappedModelRoles[mapModel_property_name] =
                {
                    modelRole,
                    property: model_propertyName
                };
        }
        
        model._expectedModelRoles = expectedModelRoles;
        model._mappedModelRoles   = mappedModelRoles;
    }
};

export class ModelConfiguration extends Configuration implements PropertyOwnerConfig {
    handlers: configurationHandlerObject = handlers;
    static Property                      = ModelProperty;
    static PropertyConfig                = ModelPropertyConfig;
    
    constructor() {
        super(...arguments);
        this.listenFor(CONFIGURATION_END,
                       null,
                       (configuredItem: Model) => {
                           const MODEL_CONFIGURED__EVENT = Model.events.CONFIG_END;
                           const eventArguments          = [configuredItem];
                           Model.eventManager.emitEvent(MODEL_CONFIGURED__EVENT, eventArguments);
                       });
    }
    
    _getConfiguredIdentifier(config) {
        return Model.identify(config.name ? `${config.name}` : null);
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        config = config || {};
        return config.inherits ? resolveInheritedConfiguration(config, Model) : Promise.resolve(config);
    }
}

makePropertyOwnerConfig(ModelConfiguration);
export default ModelConfiguration;