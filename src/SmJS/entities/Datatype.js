/**
 * @class Datatype
 * @extends ConfiguredEntity
 */
import ConfiguredEntity from "./ConfiguredEntity";

export default class Datatype extends ConfiguredEntity {
    static get name() {return 'Datatype'; }
    
    constructor(name, config) {
        super(name, config);
        /**
         * The type of Datatype this is going to be
         * @type {null}
         * @private
         */
        this._parentPromise = this._parentPromise.then(i => this._completeInit(Datatype.name));
    }
    
    inherit(item) {
        if (this._hasInheritedOnce)  return Promise.reject(new Error('Can only inherit from one Datatype'));
        this._hasInheritedOnce = true;
        return super.inherit(item);
    }
    
    getInheritables() {
        return Object.assign({}, super.getInheritables(), {
            type_name: this.type_name
        })
    }
}