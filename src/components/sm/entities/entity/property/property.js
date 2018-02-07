import {Property} from "../../property/property";

export class EntityProperty extends Property {
    _propertyType: string;
    
    toJSON() {
        return {
            ...super.toJSON(),
            propertyType: this._propertyType
        }
    }
}