// @flow

import {makeSmEntity, SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import Identity from "../../../identity/components/identity";
import {makePropertyOwner, PropertyOwner} from "../property/owner/index";
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
    
    createPropertyIdentity(propertyName: string): Identity {
        return this[SM_ID].component(propertyName);
    }
}

makeSmEntity(Entity, entityIdentity);