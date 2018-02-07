import PropertyConfig, {handlers} from "../../property/configuration";

export class ModelPropertyConfig extends PropertyConfig {
    handlers = {
        ...handlers,
    }
    
}