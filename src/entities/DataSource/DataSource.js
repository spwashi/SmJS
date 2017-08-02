/**
 * @class DataSource
 * @extends ConfiguredEntity
 */
import ConfiguredEntity from "../ConfiguredEntity";
import SymbolStore from "../../std/symbols/SymbolStore";
import TypeError from "../../errors/TypeError";
import StdError from "../../errors/Error";

const SOURCE = SymbolStore.$_$.item('_source_').Symbol;

class DataSource extends ConfiguredEntity {
    static _types;
    
    /**
     * Child classes should not override this method unless there is a good reason,
     * as subtypes are created based on the factory method
     * @return {string}
     */
    static get smID() {return 'DataSource'; }
    
    static get type() { return null; }
    
    get type() { return this.constructor.type; }
    
    constructor(name, config) {
        super(name, config);
        /**
         * The type of DataSource this is going to be
         * @type {null}
         * @private
         */
        this._type = null;
    }
    
    /**
     * Register a DataSource as a type of DataSource
     * @param {typeof DataSource}   _DataSource
     * @param {string|symbol|null}  identifier If set, this is what will be used to retrieve the DataSource type that  we register
     */
    static registerType(_DataSource, identifier = null) {
        if (!(_DataSource.prototype instanceof DataSource)) throw new TypeError("Configured 'DataSource' is not actually a DataSource");
    
        const id = identifier || _DataSource.type;
    
        if (!id) throw new StdError("Cannot register datasource without a type or identifier");
    
        this._types[id] = _DataSource;
    }
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'type'])
    }
    
    /**
     * Create a DataSource based on some sort of configuration
     *
     * @param {{}|string} _config
     * @return Promise<DataSource>
     */
    static factory(_config = {}) {
        let configType;
        if (typeof  _config === "string") {
            configType = _config;
            _config    = {};
        } else if (typeof _config !== "object") {
            throw new TypeError("Cannot build object with anything other than a string");
        }
        
        configType = configType || _config.type;
        
        if (this._types[configType]) {
            /** @type {typeof DataSource|typeof Std}  */
            const ctor = this._types[configType];
            // name is optional if it is provided in the config (assumed)
            return ctor.init(Object.assign({}, _config))
        }
        
        throw new TypeError(`Cannot build object with this configuration - ${JSON.stringify(_config)}`);
    }
    
    toJSON__type() {
        return this.type;
    }
    
    getInheritables() {
        return Object.assign({}, super.getInheritables(), {
            type: this._type
        })
    }
}

DataSource._types = {};

/**
 * @class DataSourceHaver
 * @name DataSourceHaver
 */
class DataSourceHaver extends ConfiguredEntity {
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
        if (typeof source_name !== "string") {
            throw new TypeError("Not sure how to handle dataSource configurations that aren't strings");
        }
        
        // Here, it doesn't matter if the DataSource is complete or not since that isn't our primary concern.
        return DataSource.available(source_name)
                         .then(i => {
                             /** @type {Event|DataSource}  */
                             const [e, dataSource] = i;
                             if (!(dataSource instanceof DataSource)) throw new TypeError("Returned DataSource is not of proper type");
            
                             this._dataSource = dataSource;
                             this.registerAttribute(SOURCE, dataSource);
            
                             return dataSource;
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

export default DataSource;
export {DataSourceHaver, SOURCE, DataSource};