import PropertyConfig, {handlers} from "../../property/configuration";
import {EntityProperty} from "./property";
import Identity from "../../../../identity/components/identity";

export class EntityPropertyConfig extends PropertyConfig {
    handlers = {
        ...handlers,
        role:        (role, entityProperty: EntityProperty) => {
            if (!role) return null;
            if (role !== 'value') throw new Error("The only supported EntityProperty role at the moment is 'value'");
            return entityProperty._role = role;
        },
        contexts:    (contexts, entityProperty: EntityProperty) => {
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
        },
        derivedFrom: (identity: Identity, entityProperty: EntityProperty) => {
            if (!identity) return null;
            return entityProperty._derivedFrom = identity;
        }
    }
}