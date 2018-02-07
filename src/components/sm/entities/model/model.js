// @flow

import {makeSmEntity, SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {ITEM_CONFIGURED__EVENT} from "./events";
import Identity from "../../../identity/components/identity";
import {makePropertyOwner, PropertyOwner} from "../property/owner/index";
import modelIdentity from "./identity";
import {ModelPropertyMeta} from "./property/meta";

export class Model implements SmEntity, Configurable, PropertyOwner {
    constructor() { makePropertyOwner(this, ModelPropertyMeta); }
    
    createPropertyName_Identity(propertyName: string): Identity {
        return this[SM_ID].component(propertyName);
    }
    
    toJSON() {
        return {
            smID:         this[SM_ID],
            propertyMeta: this.propertyMeta,
            properties:   this.properties
        }
    }
}

makeSmEntity(Model,
             modelIdentity,
             {
                 events: {
                     itemDoneConfiguring: ITEM_CONFIGURED__EVENT
                 }
             });