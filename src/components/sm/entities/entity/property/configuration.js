import PropertyConfig, {handlers} from "../../property/configuration";

export class EntityPropertyConfig extends PropertyConfig {
    handlers = {
        ...handlers,
        propertyType: (value: string | null, owner) => {
            if (!value) return;
            
            value = (value || '').toUpperCase();
            
            switch (value) {
                case 'CONTAINER':
                case 'ENTITY':
                    break;
                default:
                    throw new Error("Can only have properties that are containers or entities");
            }
            
            owner._propertyType = value;
        }
    }
}