const expect = require('chai').expect;
import {describe, it} from "mocha";
const _deploy = require('../../../_deploy');
describe('Std', () => {
    const Std         = _deploy.std.Std;
    const SymbolStore = _deploy.std.SymbolStore;
    const EVENTS      = _deploy.std.EventEmitter.EVENTS;
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
    })
    
});