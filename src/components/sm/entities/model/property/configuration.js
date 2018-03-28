import PropertyConfig, {handlers as prop_config_handlers} from "../../property/configuration";
import {ModelProperty} from "./property";
import PropertyAsReferenceConfiguration from "../../property/reference/configuration";
import {PropertyAsReferenceDescriptor} from "../../property/reference/index";
import {Model} from "../model";
import type {ConfigurationSession, Configuration} from "../../../../configuration/types";

export const handlers = {
    ...prop_config_handlers,
    reference: (referenceConfig, owner: ModelProperty, configuration: ConfigurationSession & Configuration) => {
        if (!referenceConfig) return null;
        const refConfig         = new PropertyAsReferenceConfiguration(referenceConfig, configuration);
        refConfig.smEntityProto = Model;
        
        return refConfig.configure(new PropertyAsReferenceDescriptor)
                        .then(descriptor => {
                            return owner._reference = descriptor;
                        })
    }
};

export class ModelPropertyConfig extends PropertyConfig {
    handlers = handlers
    
}