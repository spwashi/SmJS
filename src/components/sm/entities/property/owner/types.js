import PropertyConfig from "../configuration";
import {Property} from "../property";
import {PropertyOwner} from "./owner";

export type PropertyOwnerConfig = {
    Property: typeof Property,
    PropertyConfig: typeof PropertyConfig
}

export interface PropertyOwnerConfig {
    static Property: typeof Property;
    static PropertyConfig: typeof PropertyConfig;
    
    configureProperty(name: string, propertyConfig: {}, owner: PropertyOwner): Promise<Property>;
}