import PropertyConfig, {handlers} from "../../property/configuration";
import {Property} from "../../property/property";
import {EntityProperty} from "./property";

export class EntityPropertyConfig extends PropertyConfig {
    handlers = {
        ...handlers,
        derivedFrom:  (isGenerated, property: EntityProperty) => property._derivedFrom = isGenerated,
        propertyType: (value: string | null, property: EntityProperty) => {
            value = (value || 'property').toUpperCase();
            
            switch (value) {
                case 'CONTAINER':
                case 'PROPERTY':
                case 'ENTITY':
                    break;
                default:
                    throw new Error("Can only have properties that are containers or entities");
            }
        
            property._propertyType = value;
        }
    }
}