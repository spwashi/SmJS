import ConfiguredEntity from "./ConfiguredEntity";
import Property from "./Property";
import SymbolStore from "../std/symbols/SymbolStore";
import PropertyMetaContainer from "./PropertyMetaContainer";

const ATTRIBUTE = SymbolStore.$_$.item('_attribute_').Symbol;

export default class Model extends ConfiguredEntity {
    static get name() {return 'Model'; }
    
    constructor(name, config) {
        super(name, config);
        this._properties            = new Map;
        this._PropertyMetaContainer = new PropertyMetaContainer;
        this._parentPromise         = this._parentPromise.then(i => this.complete(Model.name));
    }
    
    get Properties() {
        return this._properties;
    }
    
    /**
     * @return {PropertyMetaContainer}
     */
    get propertyMeta() {return this._PropertyMetaContainer;}
    
    /**
     * Name Properties that we are going to register under this Model.
     * @param original_property_name
     * @return {string}
     * @private
     */
    _nameProperty(original_property_name) {
        return `{${this.name}}${original_property_name}`;
    }
    
    /**
     * Add the Property to the PropertyMeta to keep track of it.
     *
     * @param {Property} property
     * @private
     */
    _incorporatePropertyIntoMeta(property) {
        const config = property.getOriginalConfiguration();
        if (config.primary) this._PropertyMetaContainer.addPrimaryKey(property);
        
        let unique = config.unique;
        if (typeof unique !== 'string') unique = !!unique;
        if (unique === true) unique = 'unique_key';
        
        if (unique) this._PropertyMetaContainer.addUniqueKey(unique, property);
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
        this._properties[property.name] = property;
        this._incorporatePropertyIntoMeta(property);
        this.registerAttribute(original_property_name, property);
        return property;
    }
    
    addProperty(original_property_name, property_config) {
        const property_name = this._nameProperty(original_property_name);
    
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
    }
    
    set_properties(properties) {
        const promises = [];
        for (let property_name in properties) {
            if (!properties.hasOwnProperty(property_name)) continue;
            promises.push(this.addProperty(property_name, properties[property_name]));
        }
        return Promise.all(promises);
    }
}