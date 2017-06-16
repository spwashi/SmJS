import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('ConfiguredEntity', () => {
    const ConfiguredEntity = _src.entities.ConfiguredEntity;
    const Std              = _src.std.Std;
    const EVENTS           = _src.std.EventEmitter.EVENTS;
    const configuredEntity = new ConfiguredEntity('test');
    it('exists', () => {
        expect(configuredEntity.Symbol).to.be.a('symbol');
        expect(configuredEntity.Symbol.toString()).to.equal(Symbol(`[${ConfiguredEntity.name}]test`).toString())
    });
    
    it('Can resolve', d => {
        ConfiguredEntity.resolve('one').then(i => d());
        new ConfiguredEntity('one');
    });
    
    it('init.BEGIN event', d => {
        const BEGIN = Std.EVENTS.item('init').BEGIN;
        ConfiguredEntity.receive(BEGIN).then(_ => {d()});
        new ConfiguredEntity('name');
    });
    
    it('init.COMPLETE event', d => {
        const BEGIN    = Std.EVENTS.item('init').BEGIN;
        const COMPLETE = Std.EVENTS.item('init').COMPLETE;
        
        let begin_called = false,
            d_called     = false;
        
        function fn() {
            if (d_called) return;
            d_called = true;
            if (begin_called) d();
            else d('Wrong order of events');
        }
        
        ConfiguredEntity.receive(BEGIN).then(_ => fn(begin_called = true));
        ConfiguredEntity.receive(COMPLETE).then(fn);
        new ConfiguredEntity('name');
    });
    
    it('Can inherit', d => {
        const testParent = new ConfiguredEntity('parent');
        const child      = ConfiguredEntity.getSymbolStore('child').item(EVENTS);
    
        const INHERIT  = child.item(Std.EVENTS.item('inheritance').item('configuration').COMPLETE);
        const COMPLETE = child.item(Std.EVENTS.item('init').COMPLETE);
        
        let begin_called = false,
            d_called     = 0;
        
        /**
         *
         * @param event
         * @param {ConfiguredEntity}testChild
         */
        function fn(event = null, testChild = null) {
            if (++d_called === 1)return;
            let error_message;
            
            if (!begin_called) error_message = 'Wrong order of events';
            else if (!(testChild instanceof ConfiguredEntity)) error_message = ['Improper child', testChild];
            else if (!testChild.parents.has(testParent.Symbol)) error_message = 'COuld not inherit';
            
            d(error_message)
        }
        
        ConfiguredEntity.receive(INHERIT).then(_ => (begin_called = true) && fn());
        ConfiguredEntity.receive(COMPLETE, fn);
    
        const testChild = new ConfiguredEntity('child', {inherits: 'parent'});
    })
});