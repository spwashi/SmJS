import {Configurable} from "../../../../configuration/types";
import Identity from "../../../../identity/components/identity";

export class PropertyAsProxyDescriptor implements Configurable {
    _roleName: string;
    _identity: string;
    
    constructor() {}
    
    toJSON() {
        const jsonObj = {};
        if (typeof this._roleName === "string") jsonObj.roleName = this._roleName;
        if (this._identity instanceof Identity) jsonObj.identity = this._identity;
        return jsonObj
    }
}