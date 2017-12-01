import Sm from "../index";
import ConfiguredEntity from "../entities/ConfiguredEntity";

/**
 * Converts an object to a ap
 * @param configured_entity_obj
 * @return {Map<string, ConfiguredEntity>}
 * @private
 */
const _convertConfigToMap = (configured_entity_obj): Map<string, ConfiguredEntity.Configuration> => {
    /** @type {Array} structure the configuration to e a map */
    const map_prepared_obj_array = Object.keys(configured_entity_obj)
                                         .map(key => [key, configured_entity_obj[key]]);
    
    return new Map(map_prepared_obj_array);
};

/**
 * initialize an object (indexed by typical identifier) representing the SmEntity that we are initializing
 * @param configured_entity_obj
 * @param prototype
 * @return {Array}
 */
export const initializeOfType = (configured_entity_obj: Object, prototype: typeof Sm.entities.ConfiguredEntity): Array<Promise> => {
    const allModelsInitalizing = [];
    const entityConfigMap      = _convertConfigToMap(configured_entity_obj);
    
    entityConfigMap.forEach((configurationObj: ConfiguredEntity.Configuration, entityName: string) => {
        
        // set the identifier of this configuration object to be the its index in the configuration object
        configurationObj._id = configurationObj._id || entityName;
        
        // Use the prototype to create an instance of this desired type with its configuration.
        const itemPromise = prototype.init(configurationObj)
                                     .catch(i => console.error(i));
        
        allModelsInitalizing.push(itemPromise);
    });
    
    return allModelsInitalizing;
};