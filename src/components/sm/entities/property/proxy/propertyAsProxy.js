import {Configurable} from "../../../../configuration/types";
import type {SmEntity} from "../../types";
import {SM_ID} from "../../../identification";
import {Property} from "../property";

export class PropertyAsProxyDescriptor implements Configurable {
    _roleName: string;
    _proxied: SmEntity;
    _hydration: { property: Property } | undefined;
    
    constructor() {}
    
    toJSON() {
        const jsonObj = {};
        if (typeof this._roleName === "string") jsonObj.roleName = this._roleName;
        if (this._proxied && this._proxied[SM_ID]) jsonObj.identity = this._proxied[SM_ID];
        if (this._hydration && this._hydration.property) jsonObj.hydration = {property: this._hydration.property[SM_ID]};
        return jsonObj
    }
}