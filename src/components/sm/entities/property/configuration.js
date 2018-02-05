import {Configuration} from "../../../configuration/configuration";
import {SM_ID} from "../../identification";
import propertyIdentity from "./identity";
import {Property} from "./property";

export const handlers = {
    defaultValue: (defaultValue, property: Property) => property._default = defaultValue,
    primary:      (isPrimary, property: Property) => property._primary = isPrimary,
    isGenerated:  (isGenerated, property: Property) => property._isGenerated = isGenerated,
    unique:       (isUnique, property: Property) => property._unique = isUnique,
    updateValue:  (updateValue, property: Property) => property._updateValue = updateValue,
    name:         (name: string, property: Property) => property[SM_ID] = propertyIdentity.identityFor(name),
    length:       (length: number | null, property: Property) => property._length = parseInt(length) || null,
    datatypes:    (datatype: Array<string> | string, property: Property) => {
        datatype = Array.isArray(datatype) ? datatype : [datatype];
        return property._datatypes = new Set([...datatype.filter(i => !!i), ...(property._datatypes || [])])
    }
};
export default class PropertyConfig extends Configuration {
    handlers = handlers;
}