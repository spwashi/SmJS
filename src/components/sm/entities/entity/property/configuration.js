import PropertyConfig, {handlers} from "../../property/configuration";
import {Property} from "../../property/property";
import {EntityProperty} from "./property";
import Identity from "../../../../identity/components/identity";

export class EntityPropertyConfig extends PropertyConfig {
    handlers = {
        ...handlers,
        derivedFrom: (identity: Identity, entityProperty: EntityProperty) => {
            if (!identity) return null;
            return entityProperty._derivedFrom = identity;
        }
    }
}