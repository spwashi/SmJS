import Identity from "../../identity/components/identity";
import EventManager from "../../event/eventManager";
import {IdentityManager} from "../../identity/types";
import {SM_ID} from "../identification";
import type {SmEntity} from "./types";
import type {smEntityEventConfig} from "./types";

export const getSmEntityEvent =
                 (identity: Identity, eventName: string | Identity): Identity => {
                     // This is usually what holds the events
                     const $EVENTS$ = identity.component('$EVENTS$');
        
                     return $EVENTS$.instance(eventName);
                 };

const configureSmEntityEventManager = (events: smEntityEventConfig, smEntity: typeof SmEntity) => {
    const _eventManager          = smEntity.eventManager;
    const ITEM_CONFIGURED__EVENT = events.CONFIG_END;
    const onItemConfigured       =
              function (configuredEntity: SmEntity) {
                  const smID               = configuredEntity[SM_ID];
                  const configurationEvent = ITEM_CONFIGURED__EVENT.instance(smID);
            
                  // Emit an event specifically for the SmID of the item that was just configured
                  _eventManager.emitEvent(configurationEvent, configuredEntity);
              };
    
    // Add the listener onto the SmEntity
    _eventManager.createListener(ITEM_CONFIGURED__EVENT, null, onItemConfigured);
    
};

/**
 * Turn an object into an SmEntity by attaching the standard properties that would make it so
 *
 * Mixin
 *
 * @mixin SmEntity
 *
 * @param sm
 * @param sm__identity
 */
export const makeSmEntity = (sm: typeof SmEntity, sm__identity: IdentityManager) => {
    
    const events       = {
        CONFIG_END: getSmEntityEvent(sm__identity, `CONFIGURED.${sm__identity}`)
    };
    const eventManager = new EventManager;
    sm[SM_ID]          = sm__identity;
    sm.eventManager    = eventManager;
    sm.events          = events;
    sm.identify        = (name: string): Identity => sm__identity.identityFor(name);
    sm.init            =
        (item: string): Promise<Array> => {
            const configuredItemIdentity = sm.identify(item);
            const ITEM_CONFIGURED__EVENT = events.CONFIG_END;
            const event                  = ITEM_CONFIGURED__EVENT.instance(configuredItemIdentity);
            
            return eventManager.waitForEvent(event)
                               .then(([smEntityInstance]) => smEntityInstance);
        };
    
    configureSmEntityEventManager(events, sm);
};

export type {SmEntity};