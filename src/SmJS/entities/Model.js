/**
 * @class Model
 * @extends ConfiguredEntity
 */
import ConfiguredEntity from "./ConfiguredEntity";
import Property from "./Property";
import PropertyMetaContainer from "./PropertyMetaContainer";
import DataSource from "./DataSource";

export default class Model extends ConfiguredEntity {
    constructor(name, config) {
        super(name, config);
        this._properties            = new Map;
        this._PropertyMetaContainer = new PropertyMetaContainer;
        this._parentPromise         = this._parentPromise.then(i => this._completeInit(Model.name));
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
    
    /**
     *
     * @return {DataSource}
     */
    get dataSource() {return this._dataSource}
    
    //region Configure
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
    
    configure_source(source_config) {
        return this.configure_dataSource(source_config);
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
            return this._addProperty(property_name, property_config);
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
        new Property(property_name, property_config);
        
        // The Property
        return Property.resolve(property_name)
                       .then(result => {
                           /** @type {Property} property */
                           let [event, property] = result;
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