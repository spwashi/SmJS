import {Configuration} from "../../../../configuration/configuration";
import {configureName} from "../../../identification";

export default class PropertyConfig extends Configuration {
    handlers = {
        _default:  (defaultValue, owner) => {
            owner._default = defaultValue;
            return true;
        },
        name:      configureName.ofType('Property'),
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
        
        length: (length, owner) => {
            owner._length = parseInt(length) || null;
        }
    }
}