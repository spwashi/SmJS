import {Property} from "../../property/property";

export class ModelProperty extends Property {
    _propertyType: string;
    
    toJSON() {
        return {
            ...super.toJSON(),
            propertyType: this._propertyType
        }
    }
}