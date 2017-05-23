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
    
    get unique() { return this._unique || false; }
    set_unique(unique) { return Promise.resolve(this._unique = unique); }
    
    get primary() { return this._primary || false; }
    set_primary(primary) { return Promise.resolve(this._primary = primary); }
    
}