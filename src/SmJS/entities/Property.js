/**
 * @class Property
 */
import {DataSourceHaver} from "./DataSource";
export default class Property extends DataSourceHaver {
    static get name() {return 'Property'; }
    
    constructor(name, config) {
        super(name, config);
        this._parentPromise = this._parentPromise.then(i => this._sendInitComplete(Property.name));
    }
}