import ConfiguredEntity from "./ConfiguredEntity";
/**
 * @class Property
 */
export default class Property extends ConfiguredEntity {
    static get name() {return 'Property'; }
    
    constructor(name, config) {
        super(name, config);
        this._parentPromise = this._parentPromise.then(i => this.complete(Property.name));
    }
    
}