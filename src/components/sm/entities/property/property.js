import {makeSmEntity, SmEntity} from "../smEntity";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {propertyIdentity} from "./identity";
import {PropertyAsReferenceDescriptor} from "./reference/propertyAsReference";

export class Property implements SmEntity, Configurable {
    _datatypes: Set;
    _length: number;
    _default: any;
    _updateValue: any;
    _unique: boolean | string;
    _primary: boolean | string;
    _isGenerated: boolean;
    _reference: PropertyAsReferenceDescriptor;
    
    constructor() {
    }
    
    get length() {
        return this._length;
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
        this._reference && (jsonObj.reference = this._reference);
        return jsonObj
    }
}

makeSmEntity(Property, propertyIdentity);