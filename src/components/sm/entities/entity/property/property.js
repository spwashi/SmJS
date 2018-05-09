import {Property} from "../../property/property";

export class EntityProperty extends Property {
    _propertyType: string;
    _derivedFrom: string;
    _contexts: {};
    
    toJSON() {
        const obj = {...super.toJSON()};
        this._derivedFrom && (obj.derivedFrom = this._derivedFrom);
        this._contexts && (obj.contexts = this._contexts);
        this._role && (obj.role = this._role);
        
        return obj
    }
}