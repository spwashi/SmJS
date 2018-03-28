import {PropertyMeta} from "../../property/meta";
import {CONFIGURATION} from "../../../../configuration/symbols";
import {Property} from "../../property/property";

export class EntityPropertyMeta extends PropertyMeta {
    
    get index() {
        return this.findInIndex('index');
    }
    
    toJSON() {
        const index = this.toJSON__map(this.index);
        return {index}
    }
    
    incorporateProperty(property: Property): Property {
        const config = property[CONFIGURATION];
        
        if (!config.index) {
            return property;
        }
        
        let uniqueKeyName = typeof config.index === 'string' ? config.index : '_default';
        
        this._addToMapIndex('index', uniqueKeyName, property);
        return property;
    }
}