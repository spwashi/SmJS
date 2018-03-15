import Identity from "../../../../identity/components/identity";
import {PropertyMeta} from "../meta";
import {Property} from "../property";

export interface PropertyOwner {
    propertyMeta: PropertyMeta;
    properties: Object<string, Property>;
    
    addProperty(propertyName: string, property: Property): PropertyOwner;
    
    createPropertyIdentity(propertyName: string): Identity;
}

export const makePropertyOwner = (owner: PropertyOwner | { _propertyMeta: any, _properties: {} },
                                  propertyMeta: typeof PropertyMeta = PropertyMeta) => {
    const _propertyMeta = new propertyMeta;
    const _properties   = {};
    Object.defineProperties(owner, {
        propertyMeta: {get: () => _propertyMeta},
        properties:   {get: () => _properties},
        addProperty:  {
            value: (name, property) => {
                _properties[name] = property;
                return owner
            }
        }
    })
};