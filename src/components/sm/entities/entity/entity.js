// @flow

import {makeSmEntity, SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {EventManager} from "../../../event";
import {ITEM_CONFIGURED__EVENT} from "./events";
import Identity from "../../../identity/components/identity";
import {makePropertyOwner, PropertyOwner} from "../property/owner/index";
import entityIdentity from "./identity";
import {EntityPropertyMeta} from "./property/meta";

export class Entity implements SmEntity,
                               Configurable,
                               PropertyOwner {
    constructor() {
        makePropertyOwner(this, EntityPropertyMeta)
    }
    
    toJSON() {
        return {
            smID:         this[SM_ID],
            propertyMeta: this.propertyMeta,
            properties:   this.properties
        }
    }
    
    createPropertyName_Identity(propertyName: string): Identity {
        return this[SM_ID].component(propertyName);
    }
}

makeSmEntity(Entity,
             entityIdentity,
             {
                 events: {
                     itemDoneConfiguring: ITEM_CONFIGURED__EVENT
                 }
             });