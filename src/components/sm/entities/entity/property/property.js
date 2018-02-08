import {Property} from "../../property/property";

export class EntityProperty extends Property {
    _propertyType: string;
    _derivedFrom: string;
    _models: {};
    
    toJSON() {
        const obj = {
            ...super.toJSON(),
            propertyType: this._propertyType,
        };
        this._derivedFrom && (obj.derivedFrom = this._derivedFrom);
    
        return obj
    }
}