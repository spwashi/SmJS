import {Configuration} from "../../../../configuration/configuration";
import {SM_ID} from "../../../identification";
import propertyIdentity from "./identity";
import {Property} from "./property";

export default class PropertyConfig extends Configuration {
    handlers = {
        _default:    (defaultValue, property: Property) => property._default = defaultValue,
        updateValue: (updateValue, property: Property) => property._updateValue = updateValue,
        name:        (name: string, property: Property) => property[SM_ID] = propertyIdentity.create(name),
        length:      (length: number | null, property: Property) => property._length = parseInt(length) || null,
        datatypes:   (datatype: Array<string> | string, property: Property) => {
            datatype = Array.isArray(datatype) ? datatype : [datatype];
            return property._datatypes = new Set([...datatype.filter(i => !!i), ...(property._datatypes || [])])
        }
    }
}