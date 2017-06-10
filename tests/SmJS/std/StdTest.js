const expect = require('chai').expect;
import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const _src = require(SMJS_PATH);
describe('Std', () => {
    const Std         = _src.std.Std;
    const SymbolStore = _src.std.SymbolStore;
    const EVENTS      = _src.std.EventEmitter.EVENTS;
    it('Can send and receive events', done => {
        expect(1).to.equal(1);
        const tstStd = new Std;
        tstStd.receive('test').then(_ => done());
        tstStd.send('test');
    });
    it('Can send and receive SymbolStore events', done => {
        expect(1).to.equal(1);
        const tstStd               = new Std;
        const testEventSymbolStore = tstStd.EVENTS;
        tstStd.receive(testEventSymbolStore).then(_ => done());
        tstStd.send(testEventSymbolStore.item('child'));
    });
    
    it('Can resolve instances', () => {
        const name = 'boon';
        let ev     = Std.resolve(name);
        new Std(name);
        return ev;
    });
    
    it('Can resolve _properties_', _ => {
        const name = '[Std]test|title';
        
        const std     = new Std('test');
        const resolve = Std.resolve(name);
        std.registerAttribute('title', {});
        resolve.then(i => _());
    })
});