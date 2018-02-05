import Identity from "../../identity/components/identity";

export const getSmEntityEvent =
                 (identity: Identity, eventName: string | Identity): Identity => {
                     // This is usually what holds the events
                     const $EVENTS$ = identity.component('$EVENTS$');
        
                     return $EVENTS$.instance(eventName);
                 };