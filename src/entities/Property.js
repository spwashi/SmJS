/**
 * @class Property
 */
import {DataSourceHaver} from "./DataSource";
import Datatype from "../entities/Datatype";

/**
 * @name Property
 * @class Property
 * @extends DataSourceHaver
 */
export default class Property extends DataSourceHaver {
    static get smID() {return 'Property'; }
    
    /**
     * The Datatypes that this is allowed to be.
     * @return {Set}
     */
    get datatypes() {
        return this._datatypes = this._datatypes || new Set;
    }
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'datatypes'])
    }
    
    /**
     * Returns the SmIDs of the Datatypes that this Property can be
     * @return {Array}
     */
    toJSON_datatypes() {
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