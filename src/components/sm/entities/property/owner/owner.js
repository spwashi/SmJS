import Identity from "../../../../identity/components/identity";
import {PropertyMeta} from "../meta";
import {Property} from "../property";
import {SM_ID} from "../../../identification";
import type {SmEntity} from "../../types";

export interface PropertyOwner {
    propertyMeta: PropertyMeta;
    properties: Object<string, Property>;
    
    addProperty(propertyName: string, property: Property): PropertyOwner;
    
    identifyProperty(propertyName: string): Identity;
}

export const makePropertyOwner = (owner: PropertyOwner | typeof SmEntity | { _propertyMeta: any, _properties: {} },
                                  propertyMeta: typeof PropertyMeta = PropertyMeta) => {
    const _propertyMeta    = new propertyMeta;
    const _properties      = {};
    owner.identifyProperty = (entityName: Identity | string) => {
        return owner.init()
    };
    Object.defineProperties(owner, ({
        propertyMeta:     {get: () => _propertyMeta},
        properties:       {get: () => _properties},
        identifyProperty: {
            value: (propertyName: string): Identity => {
                const smID = owner[SM_ID];
                if (!smID) throw Error("Can only create Identities from SmEntities");
                const name = smID.component(propertyName);
                return Property.identify(name);
            }
        },
        addProperty:      {
            value: (name, property) => {
                _properties[name] = property;
                _propertyMeta.incorporateProperty(property);
                return owner
            }
        }
    }: PropertyOwner))
};