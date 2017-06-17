/**
 * @class Model
 * @extends ConfiguredEntity
 */
import Property from "./Property";
import PropertyMetaContainer from "./PropertyMetaContainer";
import {DataSourceHaver, SOURCE} from "./DataSource";
import {SymbolStore} from "../std/symbols/SymbolStore";
import TimeoutError from "../errors/TimeoutError";

const ATTRIBUTE = SymbolStore.$_$.item('_attribute_').Symbol;

export default class Model extends DataSourceHaver {
    constructor(name, config) {
        super(name, config);
        this._properties            = new Map;
        this._PropertyMetaContainer = new PropertyMetaContainer;
    }
    
    /**
     * @return {PropertyMetaContainer}
     */
    get propertyMeta() {return this._PropertyMetaContainer;}
    
    /**
     * Get the properties of this Model.
     * @return {Map<string|Symbol, Property>}
     * @constructor
     */
    get properties() { return this._properties; }
    
    //region Configure
    /**
     *
     * @param {Property} property
     * @return {Promise<Property>|*}
     * @private
     */
    _attachDataSourceToProperty(property) {
        return this.resolve(SOURCE)
                   .then(i => {
                       /** @type {DataSource}  */
                       const [, dataSource] = i;
                       const dsn            = dataSource.configName || dataSource.name || null;
                       return property.configure({source: dsn}).then(i => property);
                   })
                   .catch(i => {
                       const TIMEOUT = SymbolStore.$_$.item('TIMEOUT').Symbol;
                       if (i instanceof TimeoutError && i.activeSymbol instanceof SymbolStore) {
                           if (i.activeSymbol === this._symbolStore.item(ATTRIBUTE).item(SOURCE)) {
                               return property;
                           }
                       }
                       throw i;
                   });
    }
    
    /**
     * configure the properties for this Model
     * @param properties_config
     * @return {Promise.<*>}
     * @private
     */
    configure_properties(properties_config) {
        const promises = Object.entries(properties_config).map((i) => {
            let [property_name, property_config] = i;
            
            // Set the "configName" of the property. This is the name that we use to configure the property initially.
            property_config.configName = property_name;
            return this._addProperty(property_name, property_config)
                       .then(property => {
                           this._attachDataSourceToProperty(property).then(i => property).catch(i => {console.error(i) ;});
                       });
        });
        return Promise.all(promises);
    }
    
    //endregion
    
    // region Inherited
    static get name() {return 'Model'; }
    
    getInheritables() {
        return {
            properties: this._getEffectivePropertiesConfiguration()
        };
    }
    
    //endregion
    
    //region Private Methods
    /**
     * Add and register a Property, assuring that it is initialized and attached to this class.
     * @param original_property_name
     * @param property_config
     * @private
     * @return {Promise<Property>}
     */
    _addProperty(original_property_name, property_config) {
        const property_name        = this._nameProperty(original_property_name);
        property_config.configName = property_config.configName || original_property_name;
        // The Property is going to get passed on by the Property.resolve, so there is no reason to store it here
        
        // The Property
        return Property.init(property_name, property_config)
                       .then(property => {
                           /** @type {Property} property */
                           if (!(property instanceof Property)) throw new Error('Improperly created property');
                           return property;
                       })
                       .then(property => this._registerProperty(original_property_name, property))
                       .then(property => property);
    }
    
    /**
     * Get an object representation of what essentially is the original configuration that was used
     * to configure the properties of this object
     *
     * @return {{}}
     * @private
     */
    _getEffectivePropertiesConfiguration() {
        const properties = {};
        this.properties
            .forEach((property, name) => {
                properties[property.configName] = property.getOriginalConfiguration();
            });
        return properties;
    }
    
    /**
     * Name properties that we are going to register under this Model.
     * @param original_property_name
     * @return {string}
     * @private
     */
    _nameProperty(original_property_name) { return `{${this.name}}${original_property_name}`; }
    
    /**
     * Add the Property to the PropertyMeta to keep track of it.
     *
     * @param {Property} property
     * @private
     */
    _incorporatePropertyIntoMeta(property) {
        const config = property.getOriginalConfiguration();
        if (config.primary) this._PropertyMetaContainer.addPropertiesToPrimaryKey(property);
        
        let unique = config.unique;
        if (typeof unique !== 'string') unique = !!unique;
        if (unique === true) unique = 'unique_key';
        
        if (unique) this._PropertyMetaContainer.addPropertiesToUniqueKey(unique, property);
        return property;
    }
    
    /**
     * Actually register a Property under this Model. Emits the relevant registration events.
     * @param original_property_name
     * @param property
     * @return {Property}
     * @private
     */
    _registerProperty(original_property_name, property) {
        this._properties.set(property.name, property);
        this._incorporatePropertyIntoMeta(property);
        this.registerAttribute(original_property_name, property);
        return property;
    }
    
    //endregion
}