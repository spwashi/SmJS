// @flow

import {SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {Meta as PropertyMeta} from "./property/meta";
import {SM_ID} from "../../identification";

export class Model implements SmEntity, Configurable {
    [SM_ID];
    _propertyMeta: PropertyMeta;
    _properties;
    
    constructor() {
        this._propertyMeta = new PropertyMeta;
        this._properties   = {};
    }
    
    get propertyMeta() {
        return this._propertyMeta;
    }
    
    toJSON() {
        return {
            smID:         this[SM_ID],
            propertyMeta: this.propertyMeta,
            properties:   this._properties
        }
    }
    
    createPropertyName(propertyName) {
        return `{${this[SM_ID]}}${propertyName}`
    }
}