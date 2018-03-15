// @flow

import {Identifiable, identifier, IdentityManager} from "../../identity/types";
import EventManager from "../../event/eventManager";
import {SM_ID} from "../identification";
import Identity from "../../identity/components/identity";

export interface SmEntity extends Identifiable {
    [SM_ID]: Identity;
    
    static [SM_ID]: Identity;
    
    static eventManager: EventManager;
    
    static identify (name: string): Identity => {};
}

type smEntityEventConfig = { itemDoneConfiguring: Identity | undefined };

/**
 * Turn an object into an SmEntity by attaching the standard properties that would make it so
 *
 * Mixin
 *
 * @mixin SmEntity
 *
 * @param smEntityToBe
 * @param smEntityToBeIdentity
 * @param config
 */
export const makeSmEntity = (smEntityToBe: typeof SmEntity,
                             smEntityToBeIdentity: IdentityManager,
                             config: { events: smEntityEventConfig } = {events: {}}) => {
    
    const _eventManager       = new EventManager;
    smEntityToBe[SM_ID]       = smEntityToBeIdentity;
    smEntityToBe.eventManager = _eventManager;
    smEntityToBe.identify     = (name: string): Identity => smEntityToBeIdentity.identityFor(name);
    
    if (config.events) {
        if (config.events.itemDoneConfiguring) {
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
    }
};