import ConfiguredEntity from "./ConfiguredEntity";

/**
 * @class DataSource
 * @extends ConfiguredEntity
 */
export default class DataSource extends ConfiguredEntity {
    static get name() {return 'DataSource'; }
    
    
    constructor(name, config) {
        super(name, config);
        /**
         * The type of DataSource this is going to be
         * @type {null}
         * @private
         */
        this._type = null;
        this._parentPromise = this._parentPromise.then(i => this.complete(DataSource.name));
    }
    
    get type() {
        return this._type;
    }
    
    configure_type(type) {
        this._type = type;
        return Promise.resolve(type);
    }
    
    getInheritables() {
        return Object.assign({}, super.getInheritables(), {
            type: this.type
        })
    }
}