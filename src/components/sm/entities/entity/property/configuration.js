import PropertyConfig, {handlers} from "../../property/configuration";
import {EntityProperty} from "./property";
import Identity from "../../../../identity/components/identity";
import {Sm} from "../../sm";
import {parseSmID} from "../../../utility";

export class EntityPropertyConfig extends PropertyConfig {
    handlers = {
        derivedFrom: (identity: Identity, entityProperty: EntityProperty) => {
            if (!identity) return null;
            
            if (!identity.identifier) return entityProperty._derivedFrom = identity;
            
            const {owner, name} = parseSmID(identity.identifier);
            
            if (!owner) return entityProperty._derivedFrom = identity;
            
            return Sm.init(owner)
                     .then(item => {
                         item.properties[name] && (entityProperty._reference = item.properties[name]._reference);
                         item.properties[name] && (entityProperty._datatypes = item.properties[name]._datatypes);
                         item.properties[name] && (entityProperty._default = item.properties[name]._default);
                         item.properties[name] && (entityProperty._length = item.properties[name]._length);
                         return entityProperty._derivedFrom = identity;
                     });
        },
        
        ...handlers,
        
        role:      (role, entityProperty: EntityProperty) => {
            if (!role) return null;
            if (role !== 'value') throw new Error("The only supported EntityProperty role at the moment is 'value'");
            return entityProperty._role = role;
        },
        minLength: (minLength, entityProperty: EntityProperty) => {
            if (!minLength) return null;
            
            if (typeof minLength !== 'number') throw new Error("Can only use numbers to config min lengths");
            
            return entityProperty._minLength = minLength;
        },
        contexts:  (contexts, entityProperty: EntityProperty) => {
            if (!contexts) return [];
            if (!Array.isArray(contexts)) {
                throw new Error("Unsure of what to do with non-array contexts");
            }
            
            contexts.forEach(context => {
                if (typeof context !== 'string') {
                    throw new Error("Unsure of what to do with non-string contexts");
                }
            });
            
            return entityProperty._contexts = contexts;
        }
    }
}