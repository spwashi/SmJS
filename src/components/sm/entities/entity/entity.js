// @flow

import {makeSmEntity, SmEntity} from "../smEntity";
import {Configurable} from "../../../configuration/types";
import {SM_ID} from "../../identification";
import {makePropertyOwner, PropertyOwner} from "../property/owner/owner";
import entityIdentity from "./identity";
import {EntityPropertyMeta} from "./property/meta";
import Identity from "../../../identity/components/identity";

export class Entity implements SmEntity, Configurable, PropertyOwner {
    _persistedIdentity: Identity;
    _representations: Object;
    
    constructor() {
        makePropertyOwner(this, EntityPropertyMeta)
    }
    
    get persistedIdentity() {
        return this._persistedIdentity;
    }
    
    toJSON() {
        const entity = {
            smID:       this[SM_ID],
            properties: this.properties
        };
        if (this.propertyMeta.toJSON()) entity.propertyMeta = this.propertyMeta;
        if (this._persistedIdentity) entity.persistedIdentity = this._persistedIdentity;
        if (this._representations) entity.representations = this._representations;
        return entity
    }
}

makeSmEntity(Entity, entityIdentity);