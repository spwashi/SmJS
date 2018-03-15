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
import modelIdentity from "../model/identity";
import {Model} from "../model/model";

export class Entity implements SmEntity, Configurable, PropertyOwner {
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
    
    createPropertyIdentity(propertyName: string): Identity {
        return this[SM_ID].component(propertyName);
    }
}

makeSmEntity(Entity, entityIdentity);