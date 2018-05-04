import {makeSmEntity, SmEntity} from "../smEntity";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {propertyIdentity} from "./identity";
import {PropertyAsReferenceDescriptor} from "./reference/index";

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
    
    get reference(): PropertyAsReferenceDescriptor {
        return this._reference;
    }
    
    toJSON() {
        const jsonObj = {
            smID: this[SM_ID],
        };
        this._datatypes && this._datatypes.size && (jsonObj.datatypes = [...this._datatypes]);
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