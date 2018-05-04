import {Configurable} from "../../../../configuration/types";
import type {SmEntity} from "../../types";
import {SM_ID} from "../../../identification";

export class PropertyAsReferenceDescriptor implements Configurable {
    _proxied: SmEntity;
    _hydration: { property: SmEntity } | undefined;
    
    constructor() {}
    
    get hydrationMethod() {
        return this._hydration && this._hydration.property && this._hydration.property[SM_ID];
    }
    
    toJSON() {
        const jsonObj = {};
        if (this._proxied && this._proxied[SM_ID]) jsonObj.identity = this._proxied[SM_ID];
        if (this.hydrationMethod) jsonObj.hydrationMethod = this.hydrationMethod;
        return jsonObj
    }
}