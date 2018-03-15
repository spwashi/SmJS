import {Configurable} from "../../../../configuration/types";

export class PropertyAsProxyDescriptor implements Configurable {
    constructor() {}
    
    toJSON() {
        const jsonObj = {};
        return jsonObj
    }
}