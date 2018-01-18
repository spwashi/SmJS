// @flow

import {SmEntity} from "../../types";
import {Configurable} from "../../../../configuration/types";
import {SM_ID} from "../../../identification";
import {createIdentityManager} from "../../../../identity/components/identity";

export class Property implements SmEntity, Configurable {
    _datatypes: Set;
    _length: number;
    _default: any;
    
    toJSON() {
        const jsonObj = {
            datatypes: [...this._datatypes],
            smID:      this[SM_ID]
        };
        this._length && (jsonObj.length = this._length);
        this._default && (jsonObj.defaultValue = this._default);
        return jsonObj
    }
}
