import {Configuration} from "../../../configuration/configuration";
import {SM_ID} from "../../identification";
import propertyIdentity from "./identity";
import {Property} from "./property";
import type {ConfigurationSession} from "../../../configuration/types";
import {Model} from "../model/model";
import {PropertyAsReferenceDescriptor} from "./reference";
import PropertyAsReferenceConfiguration from "./reference/configuration";
import {Sm, SmEntity} from "../smEntity";
import {parseSmID} from "../../utility";

export const handlers = {
    defaultValue: (defaultValue, property: Property) => property._default = defaultValue,
    primary:      (isPrimary, property: Property) => property._primary = isPrimary,
    isGenerated:  (isGenerated, property: Property) => property._isGenerated = !!isGenerated,
    unique:       (isUnique, property: Property) => property._unique = isUnique,
    updateValue:  (updateValue, property: Property) => property._updateValue = updateValue,
    length:       (length: number | null, property: Property) => property._length = parseInt(length) || null,
    name:         (name: string, property: Property) => property[SM_ID] = Property.identify(name),
    datatypes:    (datatype: Array<string> | string, property: Property) => {
        datatype = Array.isArray(datatype) ? datatype : [datatype];
        return property._datatypes = new Set([...datatype.filter(i => !!i), ...(property._datatypes || [])])
    },
    reference:    (referenceConfig, owner: Property, configuration: ConfigurationSession & Configuration) => {
        if (!referenceConfig) return null;
        const session: ConfigurationSession = configuration;
        return session.waitFor('name')
                      // Use the owner of the property as the manager of the identity?
                      .then(name => Sm.identify(referenceConfig.identity || parseSmID(`${name}`).owner))
                      .then((Manager: SmEntity) => {
                                const refConfig         = new PropertyAsReferenceConfiguration(referenceConfig, configuration);
                                refConfig.smEntityProto = Manager;
                                return refConfig.configure(new PropertyAsReferenceDescriptor)
                                                .then(descriptor => {
                                                    return owner._reference = descriptor;
                                                });
                            }
                      )
    }
};
export default class PropertyConfig extends Configuration {
    handlers = handlers;
}