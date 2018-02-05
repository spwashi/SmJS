// @flow

import {makeSmEntity, SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {createIdentityManager} from "../../../identity/components/identity";
import {propertyIdentity} from "./identity";

export class Property implements SmEntity, Configurable {
    _datatypes: Set;
    _length: number;
    _default: any;
    _updateValue: any;
    _unique: boolean | string;
    _primary: boolean | string;
    _isGenerated: boolean;
    
    constructor() {
    }
    
    toJSON() {
        const jsonObj = {
            datatypes: [...this._datatypes],
            smID:      this[SM_ID],
        };
        this._length && (jsonObj.length = this._length);
        this._primary && (jsonObj.primary = this._primary);
        this._unique && (jsonObj.unique = this._unique);
        this._isGenerated && (jsonObj.isGenerated = this._isGenerated);
        this._default && (jsonObj.defaultValue = this._default);
        this._updateValue && (jsonObj.updateValue = this._updateValue);
        return jsonObj
    }
}

makeSmEntity(Property, propertyIdentity);