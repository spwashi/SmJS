import ConfiguredEntity from "./ConfiguredEntity";
import Property from "./Property";
export default class Model extends ConfiguredEntity {
    static get name() {return 'Model'; }
    
    constructor(name, config) {
        super(name, config);
        this._Properties    = new Map;
        this._parentPromise = this._parentPromise
                                  .then(i => this.complete(Model.name));
    }
    
    addProperty(property_name, property_config) {
        property_name = `{${this.name}}${property_name}`;
        new Property(property_name, property_config);
        
        return Property.resolve(property_name).then(result => {
            let [event, property] = result;
            if (!(property instanceof Property)) throw new Error('Improperly created property');
            this._Properties[property.name] = property;
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