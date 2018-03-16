import {Configurable} from "../../../../configuration/types";
import type {SmEntity} from "../../types";
import {SM_ID} from "../../../identification";

export class PropertyAsReferenceDescriptor implements Configurable {
    _proxied: SmEntity;
    _hydration: { property: SmEntity } | undefined;
    
    constructor() {}
    
    toJSON() {
        const jsonObj = {};
        if (this._proxied && this._proxied[SM_ID]) jsonObj.identity = this._proxied[SM_ID];
        if (this._hydration && this._hydration.property) jsonObj.hydrationMethod = this._hydration.property[SM_ID];
        return jsonObj
    }
}