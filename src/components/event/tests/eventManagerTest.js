import {describe, it} from "mocha";
import EventManager from "../eventManager";

describe('eventManager', () => {
    it('canLogEvents', done => {
        const eventManager = new EventManager();
        
        const IN_GENERAL    = 'in general';
        const OF_WINE       = 'wine';
        let lastSipped;
        const finishInOrder = beverage => {
            if (lastSipped && beverage === IN_GENERAL) throw new Error("Expected WINE");
            if (!lastSipped && beverage !== IN_GENERAL) throw new Error("Expected other beverage (general) got " + beverage);
            if (lastSipped !== IN_GENERAL && beverage === OF_WINE) throw new Error("Expected other beverage");
            if (beverage === OF_WINE) done();
            lastSipped = beverage;
        };
        
        const EVENT__sip_from_glass = 'took a sip from a glass';
        
        eventManager.emitEvent(EVENT__sip_from_glass, ['water']);
        eventManager.emitEvent(EVENT__sip_from_glass, ['coca-cola']);
        eventManager.emitEvent(EVENT__sip_from_glass, ['beer']);
        
        eventManager.waitForEvent(EVENT__sip_from_glass, false, ['wine']).then(i => finishInOrder(OF_WINE));
        eventManager.waitForEvent(EVENT__sip_from_glass).then(i => finishInOrder(IN_GENERAL));
        
        eventManager.emitEvent(EVENT__sip_from_glass, ['coca-cola']);
        eventManager.emitEvent(EVENT__sip_from_glass, ['wine']);
    });
    
    it('Can create emitters', done => {
        const eventManager = new EventManager;
        
        const EVENT__dance  = 'dance';
        const firstArgument = 'arg';
        
        const emitDance = eventManager.createEmitter(EVENT__dance, [firstArgument]);
        eventManager.waitForEvent(EVENT__dance, true, [firstArgument, 'second'])
                    .then(([arg1, arg2]) => {
                        arg1 === firstArgument && arg2 === 'second' && done()
                    });
        emitDance('first');
        emitDance('second');
        
    });
    
    it('Can create listeners', done => {
        
        const eventManager        = new EventManager;
        const EVENT__do_something = 'do something';
        
        eventManager.createListener(EVENT__do_something, ['argument2'], () => done());
        eventManager.emitEvent(EVENT__do_something, ['argument1']);
        eventManager.emitEvent(EVENT__do_something, ['argument2']);
    })
});