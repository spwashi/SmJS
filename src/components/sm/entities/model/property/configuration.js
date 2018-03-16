import PropertyConfig, {handlers as prop_config_handlers} from "../../property/configuration";
import {ModelProperty} from "./property";
import PropertyAsReferenceConfiguration from "../../property/reference/configuration";
import {PropertyAsReferenceDescriptor} from "../../property/reference/propertyAsReference";
import {Model} from "../model";

export const handlers = {
    ...prop_config_handlers,
    reference: (referenceConfig, owner: ModelProperty) => {
        if (!referenceConfig) return null;
        const refConfig         = new PropertyAsReferenceConfiguration(referenceConfig);
        refConfig.smEntityProto = Model;
        
        return refConfig.configure(new PropertyAsReferenceDescriptor)
                        .then(descriptor => {
                            console.log(descriptor);
                            return owner._reference = descriptor;
                        })
    }
};

export class ModelPropertyConfig extends PropertyConfig {
    handlers = handlers
    
}