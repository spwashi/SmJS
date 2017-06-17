/**
 * @class Datatype
 * @extends ConfiguredEntity
 */
import ConfiguredEntity from "./ConfiguredEntity";
import {GenericError} from "../errors/Error";

export default class Datatype extends ConfiguredEntity {
    static get name() {return 'Datatype'; }
    
    constructor(name, config) {
        super(name, config);
    }
    
    inherit(item) {
        if (this._hasInheritedOnce)  return Promise.reject(new GenericError('Can only inherit from one Datatype', this.symbolStore));
        this._hasInheritedOnce = true;
        return super.inherit(item);
    }
    
    getInheritables() {
        return Object.assign({}, super.getInheritables(), {
            type_name: this.type_name
        })
    }
}