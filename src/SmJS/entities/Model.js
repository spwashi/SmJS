import ConfiguredEntity from "./ConfiguredEntity";
import Property from "./Property";
import SymbolStore from "../std/symbols/SymbolStore";
import PropertyMetaContainer from "./PropertyMetaContainer";

const ATTRIBUTE = SymbolStore.$_$.item('_attribute_').Symbol;

/**
 * @class Model
 * @extends ConfiguredEntity
 */
export default class Model extends ConfiguredEntity {
    static get name() {return 'Model'; }
    
    constructor(name, config) {
        super(name, config);
        this._properties            = new Map;
        this._PropertyMetaContainer = new PropertyMetaContainer;
        this._parentPromise         = this._parentPromise.then(i => this._complete(Model.name));
    }
    
    /**
     * Get the properties of this Model.
     * @return {Map<string|Symbol, Property>}
     * @constructor
     */
    get properties() { return this._properties; }
    
    /**
     * @return {PropertyMetaContainer}
     */
    get propertyMeta() {return this._PropertyMetaContainer;}
    
    getInheritables() {
        return {
            properties: this._getEffectivePropertiesConfiguration()
        };
    }
    
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
    
    //region Private Methods
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
    
    /**
     * configure the properties for this Model
     * @param properties
     * @return {Promise.<*>}
     * @private
     */
    configure_properties(properties) {
        const promises = Object.entries(properties).map((i) => {
            let [property_name, property_config] = i;
            property_config.configName           = property_name;
            // console.log(property_config, this.Symbol);
            return this._addProperty(property_name, property_config);
        });
        return Promise.all(promises);
    }
    
    //endregion
}