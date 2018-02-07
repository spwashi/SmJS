import {PropertyMeta} from "../../property/meta";
import {CONFIGURATION} from "../../../../configuration/configuration";
import {Property} from "../../property/property";

export class ModelPropertyMeta extends PropertyMeta {
    
    get primary() {
        return this.findInIndex('primary');
    }
    
    get unique() {
        return this.findInIndex('unique');
    }
    
    toJSON() {
        return {
            primary: this._toJSON__set(this.primary),
            unique:  this.toJSON__map(this.unique)
        }
    }
    
    incorporateProperty(property: Property): Property {
        const config                = property[CONFIGURATION];
        const [isUnique, isPrimary] = [config.unique, config.primary];
        
        if (isPrimary) {
            this._addToSetIndex('primary', property);
        }
        
        if (!!isUnique) {
            let uniqueKeyName = typeof isUnique !== 'string' ? 'unique_key' : isUnique;
            
            this._addToMapIndex('unique', uniqueKeyName, property);
        }
        
        return property;
    }
}