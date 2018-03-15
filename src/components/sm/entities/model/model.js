import {makeSmEntity, SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {ITEM_CONFIGURED__EVENT} from "./events";
import {Identity} from "../../../identity";
import {makePropertyOwner, PropertyOwner} from "../property/owner/index";
import modelIdentity from "./identity";
import {ModelPropertyMeta} from "./property/meta";
import {mappedModelRoleObject, ModelRole} from "./role";

export class Model implements SmEntity, Configurable, PropertyOwner {
    [SM_ID]: Identity;
    
    _mappedModelRoles: Object<string, mappedModelRoleObject>;
    _expectedModelRoles: Object<string, ModelRole>;
    
    constructor() { makePropertyOwner(this, ModelPropertyMeta); }
    
    createPropertyIdentity(propertyName: string): Identity {
        return this[SM_ID].component(propertyName);
    }
    
    toJSON() {
        const json_obj = {smID: this[SM_ID]};
        
        !this.propertyMeta.isEmpty() && (json_obj.propertyMeta = this.propertyMeta);
        Object.keys(this.properties).length && (json_obj.properties = this.properties);
        this._mappedModelRoles && (json_obj.mappedModelRoles = this._mappedModelRoles);
        
        return json_obj
    }
}

makeSmEntity(Model, modelIdentity);