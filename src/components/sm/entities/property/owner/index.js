import Identity from "../../../../identity/components/identity";
import {PropertyMeta} from "../meta";
import {Property} from "../property";

export interface PropertyOwner {
    propertyMeta: PropertyMeta;
    properties: Object<string, Property>;
    
    addProperty(propertyName: string, property: Property): PropertyOwner;
    
    createPropertyName_Identity(propertyName: string): Identity;
}

export const makePropertyOwner = (owner: PropertyOwner | { _propertyMeta: any, _properties: {} }) => {
    const _propertyMeta = new PropertyMeta;
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