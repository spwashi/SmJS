// @flow

import {Identifiable, identifier, IdentityManager} from "../../identity/types";
import EventManager from "../../event/eventManager";
import {SM_ID} from "../identification";
import Identity from "../../identity/components/identity";

export interface SmEntity extends Identifiable {
    static [SM_ID]: Identity;
    
    [SM_ID]: Identity;
    
    static eventManager: EventManager;
}

export const makeSmEntity = (smEntityToBe,
                             smEntityToBeIdentity: Identity,
                             config: {
                                 events: {
                                     itemDoneConfiguring: Identity | undefined
                                 }
                             } = {events: {}}) => {
    
    const _eventManager = new EventManager;
    
    Object.defineProperties(smEntityToBe, {
        eventManager: {get: () => _eventManager},
    });
    
    if (config.events && config.events.itemDoneConfiguring) {
        const ITEM_CONFIGURED__EVENT = config.events.itemDoneConfiguring;
        
        _eventManager
            .createListener(ITEM_CONFIGURED__EVENT,
                            null,
                            (configuredEntity: SmEntity) => {
                                const smID               = configuredEntity[SM_ID];
                                const configurationEvent = ITEM_CONFIGURED__EVENT.instance(smID);
                                _eventManager.logEvent(configurationEvent, configuredEntity);
                            });
    }
};