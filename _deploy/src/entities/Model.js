import ConfiguredEntity from "./ConfiguredEntity";
import Property from "./Property";
import SymbolStore from "../std/symbols/SymbolStore";

const PROPERTY = SymbolStore.$_$.item('_PROPERTY_').Symbol;

export default class Model extends ConfiguredEntity {
    static get name() {return 'Model'; }
    
    get Properties() {
        return this._Properties;
    }
    
    constructor(name, config) {
        super(name, config);
        this._Properties    = new Map;
        this._parentPromise = this._parentPromise.then(i => this.complete(Model.name));
    }
    
    addProperty(original_property_name, property_config) {
        const property_name = `{${this.name}}${original_property_name}`;
        new Property(property_name, property_config);
        
        return Property.resolve(property_name).then(result => {
            /** @type {Property} property */
            let [event, property] = result;
            if (!(property instanceof Property)) throw new Error('Improperly created property');
    
            this._Properties[property.name] = property;
            this.register_attribute(original_property_name, property);
            return property;
        })
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