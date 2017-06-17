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
    static get name() {return 'Property'; }
    
    /**
     * @return {Set}
     */
    get datatypes() {
        return this._datatypes = this._datatypes || new Set;
    }
    
    configure_datatypes(datatype) {
        datatype       = Array.isArray(datatype) ? datatype : [datatype];
        const promises = datatype.filter(i => !!i).map(dt => Datatype.resolve(dt).then(event__dt => event__dt[1]));
        return Promise.all(promises)
                      .then(datatypes => this._datatypes = new Set([...datatypes.map(dt => dt.Symbol)]))
    }
}