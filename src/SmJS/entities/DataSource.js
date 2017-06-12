import ConfiguredEntity from "./ConfiguredEntity";

/**
 * @class DataSource
 * @extends ConfiguredEntity
 */
export default class DataSource extends ConfiguredEntity {
    static get name() {return 'DataSource'; }
    
    /**
     *
     * @param {Map|{}} value
     */
    static set acceptedTypes(value) {
        if (typeof value === 'object') value = new Map(Object.entries(value));
        if (!(value instanceof Map)) throw new TypeError("Cannot set Accepted Types to be anything but a map.");
        this._acceptedTypes = value;
    }
    
    /**
     * Get a Map of the Types that are allowed
     * @return {Map|{}}
     */
    static get acceptedTypes() {
        return this._acceptedTypes = this._acceptedTypes || new Map;
    }
    
    constructor(name, config) {
        super(name, config);
        /**
         * The type of DataSource this is going to be
         * @type {null}
         * @private
         */
        this._type = null;
        this._parentPromise = this._parentPromise.then(i => this._completeInit(DataSource.name));
    }
    
    get type() {
        return this._type;
    }
    
    configure_type(type) {
        this._type = type;
        if (typeof type !== "string") throw  new TypeError("Can only use strings as types");
        if (typeof this.constructor.acceptedTypes.get(type) === "undefined") {
            throw new TypeError("Could not set type to " + type + ' - it has not been configured yet.');
        }
        return Promise.resolve(type);
    }
    
    getInheritables() {
        return Object.assign({}, super.getInheritables(), {
            type: this.type
        })
    }
}
/**
 * @class DataSourceHaver
 * @name DataSourceHaver
 */
export class DataSourceHaver extends ConfiguredEntity {
    /**
     *
     * @return {DataSource}
     */
    get dataSource() {return this._dataSource}
    
    /**
     * @name DataSourceHaver.configure_dataSource
     * @param source_name
     * @return {Promise<DataSource>}
     */
    configure_dataSource(source_name) {
        if (typeof source_name !== "string") throw new TypeError("Not sure how to handle dataSource configurations that aren't strings");
        
        // Here, it doesn't matter if the DataSource is complete or not since that isn't our primary concern.
        return DataSource.available(source_name)
                         .then(i => {
                             /** @type {Event|DataSource}  */
                             const [e, dataSource] = i;
            
                             if (!(dataSource instanceof DataSource)) {
                                 throw new TypeError("Returned DataSource is not of proper type");
                             }
                             return this._dataSource = dataSource;
                         });
    }
    
    /**
     * @alias DataSourceHaver.configure_dataSource
     * @param source_config
     * @return {Promise<DataSource>}
     */
    configure_source(source_config) {
        return this.configure_dataSource(source_config);
    }
}