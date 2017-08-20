/**
 * @class Entity
 */
import ConfiguredEntity from "../ConfiguredEntity";
import EntityProperty from "./EntityProperty";

/**
 * @name Entity
 * @class Entity
 * @extends ConfiguredEntity
 */
export default class Entity extends ConfiguredEntity {
    static get smID() {return 'Entity'; }
    
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
    
    toJSON__length() {
        return this._length = this._length || null;
    }
    
    configure_length(length) {
        this._length = parseInt(length);
    }
    
    /**
     * Returns the SmIDs of the Datatypes that this Entity can be
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

Entity.EntityProperty = EntityProperty;