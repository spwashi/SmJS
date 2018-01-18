import {Configuration} from "../../../../configuration/configuration";
import {SM_ID} from "../../../identification";
import propertyIdentity from "./identity";

export default class PropertyConfig extends Configuration {
    handlers = {
        _default:  (defaultValue, owner) => {
            owner._default = defaultValue;
            return true;
        },
        name:      (name, property) => property[SM_ID] = propertyIdentity.create(name),
        /**
         *
         * @param datatype
         * @param owner
         * @return {Promise<Set>}
         */
        datatypes: (datatype, owner) => {
            const self      = owner;
            datatype        = Array.isArray(datatype) ? datatype : [datatype];
            self._datatypes = new Set([...datatype.filter(i => !!i), ...(self._datatypes || [])])
        },
        length:    (length, owner) => {
            owner._length = parseInt(length) || null;
        }
    }
}