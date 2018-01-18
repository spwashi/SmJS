// @flow

import {SmEntity} from "../types";
import {Configurable} from "../../../configuration/types";
import {Meta as PropertyMeta} from "./property/meta";
import {createName, SM_ID} from "../../identification";
import {EventManager} from "../../../event";
import {CONFIGURED_MODEL} from "./events";
import {createIdentityManager} from "../../../identity/components/identity";

export class Model implements SmEntity, Configurable {
    /** @private */
    static _eventManager = new EventManager;
    [SM_ID];
    _properties;
    _propertyMeta: PropertyMeta;
    
    constructor() {
        this._propertyMeta = new PropertyMeta;
        this._properties   = {};
    }
    
    static get eventManager(): EventManager {
        return this._eventManager;
    }
    
    get propertyMeta() {
        return this._propertyMeta;
    }
    
    toJSON() {
        return {
            smID:         this[SM_ID],
            propertyMeta: this._propertyMeta,
            properties:   this._properties
        }
    }
    
    createPropertyName(propertyName) {
        return this[SM_ID].item(propertyName);
    }
}

Model.eventManager
     .createListener(CONFIGURED_MODEL, null, configuredModel => {
         const smID = configuredModel[SM_ID];
         Model.eventManager.logEvent(CONFIGURED_MODEL.item(smID), configuredModel);
         // console.log('LOGGING CONFIGURED: ', smID);
     });