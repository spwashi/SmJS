import {EntityProperty} from "./source";
import {Configuration} from "../../../../../configuration";

export class EntityPropertySourceConfig extends Configuration {
    handlers = {
        propertyType: (value: string | null, property: EntityPropertySource) => {
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