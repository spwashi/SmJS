import {Configurable} from "../../../../configuration/types";

export class PropertyAsProxyDescriptor implements Configurable {
    _roleName: string;
    _identity: string;
    
    constructor() {}
    
    toJSON() {
        const jsonObj = {};
        if (typeof this._roleName === "string") jsonObj.roleName = this._roleName;
        return jsonObj
    }
}