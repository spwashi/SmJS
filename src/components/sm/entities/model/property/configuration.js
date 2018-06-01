import PropertyConfig, {handlers as prop_config_handlers} from "../../property/configuration";
import PropertyAsReferenceConfiguration from "../../property/reference/configuration";
import {PropertyAsReferenceDescriptor} from "../../property/reference/index";
import {Model} from "../model";
import type {ConfigurationSession, Configuration} from "../../../../configuration/types";
import {Property} from "../../..";

export const handlers = {
    ...prop_config_handlers,
};

export class ModelPropertyConfig extends PropertyConfig {
    handlers = handlers
    
}