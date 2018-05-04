import {
    Configuration,
    CONFIGURATION_END,
    resolveInheritedConfiguration
} from "../../../configuration/configuration";
import {Entity} from "./entity";
import {SM_ID} from "../../identification";
import {makePropertyOwnerConfig} from "../property/owner/configuration";
import {EntityProperty} from "./property/property";
import {EntityPropertyConfig} from "./property/configuration";
import type {PropertyOwnerConfig} from "../property/owner/configuration";
import type {configurationHandlerObject, ConfigurationSession} from "../../../configuration/types";
import {Model} from "../..";
import modelIdentity from "../model/identity";
import {ModelProperty} from "../model/property";
import {CONFIGURATION} from "../../../configuration/symbols";
import Identity from "../../../identity/components/identity";

let getPersistedIdentityModel = function (configuration) {
    if (!configuration.configurationObject.persistedIdentity) {
        console.log(configuration.configurationObject);
        throw new Error("Can only have 'true' properties alongside a persisted identity");
    }
    return configuration.waitFor('persistedIdentity')
                        .then(([modelIdentity]) => {
                            return Model.init(modelIdentity);
                        });
};

function configureStdInheritedProperty(configuration, name, entity, propertyConfig) {
    return getPersistedIdentityModel(configuration).then((model: Model) => {
        const properties               = model.properties;
        const property: ModelProperty  = properties[name];
        const newPropertyConfiguration = {
            ...(property[CONFIGURATION] || {}),
            ...propertyConfig,
            derivedFrom: property[SM_ID]
        };
        return configuration.configureProperty(name,
                                               newPropertyConfiguration,
                                               entity)
    });
}

let resolvePropertyDerivation     = function (propertyConfig, identitySmEntity) {
    Object.entries(propertyConfig.derivedFrom)
          .forEach(([name, derivedFrom]) => {
              if (derivedFrom === true) {
                  const model    = identitySmEntity;
                  const property = model.properties[name];
                  if (!property) {
                      console.log(Object.keys(model.properties), name);
                      throw new Error("Property " + name + " not found on model")
                  }
                  const reference = property.reference;
            
                  if (!reference) {
                      throw new Error("Derived properties need a reference")
                  }
            
                  if (propertyConfig.identity instanceof Identity) {
                      propertyConfig.datatypes = propertyConfig.identity;
                  }
                  return propertyConfig.derivedFrom[name] = reference.hydrationMethod;
              }
          })
};
let configurePropertyWithIdentity = function (propertyConfig, configuration, name, entity) {
    if (propertyConfig.identity === true) {
        return configureStdInheritedProperty(configuration, name, entity, {...propertyConfig, identity: undefined});
    }
    
    const smEntityResolved = Promise.race([
                                              Entity.init(propertyConfig.identity),
                                              Model.init(propertyConfig.identity)
                                          ]);
    return smEntityResolved.then(identitySmEntity => {
                               if (propertyConfig.derivedFrom) {
                                   if (typeof propertyConfig.derivedFrom === "object") {
                                       const model = identitySmEntity instanceof Model ? identitySmEntity
                                                                                       : Model.init((identitySmEntity: Entity).persistedIdentity);
                                       return Promise.resolve(model)
                                                     .then(model => {
                                                         resolvePropertyDerivation(propertyConfig, model);
                                                         return propertyConfig;
                                                     })
                                   }
                               }
                               return propertyConfig;
                           })
                           .then(propertyConfig => {
                               return configuration.configureProperty(name, propertyConfig, entity)
                           });
};
const handlers                    = {
    name:              (name, entity) => entity[SM_ID] = Entity.identify(name),
    persistedIdentity: (identity: Identity, entity: Entity) => {
        if (!identity) return;
        console.log(identity);
        return Model.init(identity)
                    .then((model: Model) => {
                        return entity._persistedIdentity = model[SM_ID];
                    });
    },
    representations:   (representations, entity: Entity) => {
        if (!representations) return;
        entity._representations = Object.entries(representations)
                                        .map(([name, representation]) => {
                                            if (!representation || typeof representation !== "object") {
                                                throw new Error("Can only configure with objects")
                                            }
                                            return {
                                                name,
                                                datatype:          null,
                                                requireFormatting: false,
                                                ...representation,
                                            }
                                        })
                                        .reduce((all, {name: k, ...v}) => (all[k] = v, all), {})
    },
    properties:        (allPropertiesConfig: {} | any = {}, entity: Entity, configuration: ConfigurationSession & EntityConfiguration) => {
        const entries  = Object.entries(allPropertiesConfig);
        const promises = entries.map(([name, propertyConfig]) => {
            if (propertyConfig === true) {
                return configureStdInheritedProperty(configuration, name, entity);
            }
            
            if (!(propertyConfig && typeof propertyConfig === "object")) {
                throw new Error("Could not configure property with - " + JSON.stringify(propertyConfig));
            }
            
            if (propertyConfig.identity) {
                return configurePropertyWithIdentity(propertyConfig, configuration, name, entity);
            }
            return configuration.configureProperty(name, propertyConfig, entity);
        });
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
                           Entity.eventManager.emitEvent(ENTITY_CONFIGURED__EVENT, eventArguments);
                       });
    }
    
    resolveConfiguration(config, owner: {}): Promise<Object> {
        config = config || {};
        return config.inherits ? resolveInheritedConfiguration(config, Entity) : Promise.resolve(config);
    }
}

makePropertyOwnerConfig(EntityConfiguration);