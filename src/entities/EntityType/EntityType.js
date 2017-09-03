/**
 * @class EntityType
 */
import ConfiguredEntity from "../ConfiguredEntity";
import EntityTypeProperty from "./EntityTypeProperty";
import {PropertyHaverConfigurationExtender, PropertyHaverExtender} from "../Property/PropertyHaver";
import Configuration from "../Configuration";

class EntityTypeConfiguration extends PropertyHaverConfigurationExtender(Configuration) {

}

/**
 * @name EntityType
 * @class EntityType
 * @extends ConfiguredEntity
 * @extends PropertyHaver
 */
export default class EntityType extends PropertyHaverExtender(ConfiguredEntity) {
    static smID          = 'EntityType';
    static Configuration = EntityTypeConfiguration;
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'datatypes', '?length'])
    }
    
    /**
     * The Datatypes that this is allowed to be.
     * @return {Set}
     */
    get datatypes() {
        return this._datatypes = this._datatypes || new Set;
    }
    
    /**
     * Get the Property type that we are going to use
     * @param property_config
     * @return {typeof Property}
     */
    getPropertyType(property_config): typeof EntityTypeProperty {
        return EntityTypeProperty;
    }
    
    toJSON__length() {
        return this._length = this._length || null;
    }
    
    configure_length(length) {
        this._length = parseInt(length);
    }
    
    /**
     * Returns the SmIDs of the Datatypes that this EntityType can be
     * @return {Array}
     */
    toJSON__datatypes() {
        return [...this.datatypes].map(dt => dt.smID);
    }
    
    /**
     *
     * @param datatype
     * @return {Promise.<Set>}
     */
    configure_datatypes(datatype) {
        datatype       = Array.isArray(datatype) ? datatype : [datatype];
        const promises = datatype.filter(i => !!i).map(dt => Datatype.resolve(dt).then(event__dt => event__dt[1]));
        return Promise.all(promises)
                      .then(datatypes => this._datatypes = new Set([...datatypes]))
    }
    
}

EntityType.EntityTypeProperty = EntityTypeProperty;