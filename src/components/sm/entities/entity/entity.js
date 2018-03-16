// @flow

import {makeSmEntity, SmEntity} from "../smEntity";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import Identity from "../../../identity/components/identity";
import {makePropertyOwner, PropertyOwner} from "../property/owner/owner";
import entityIdentity from "./identity";
import {EntityPropertyMeta} from "./property/meta";

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
}

makeSmEntity(Entity, entityIdentity);