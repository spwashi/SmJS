import {describe, it} from "mocha";
const expect  = require('chai').expect;
const _deploy = require('../../../_deploy');

describe('EventEmitter', () => {
    const EventEmitter     = _deploy.std.EventEmitter;
    const testEventEmitter = new EventEmitter;
    
    it('can emit strings', done => {
        const event = 'test';
        testEventEmitter.on(event, i => done());
        testEventEmitter.emit(event);
    });
    
    it('can emit symbols', done => {
        const event = Symbol('test');
        testEventEmitter.on(event, i => done());
        testEventEmitter.emit(event);
    });
    
    const SymbolStore     = _deploy.std.symbols.SymbolStore;
    const testSymbolStore = SymbolStore.init('testSymbolStore');
    it('Can emit SymbolStores', (done) => {
        const event = testSymbolStore;
        testEventEmitter.once({}, () => {throw new Error('We are just stringifying objects')});
        testEventEmitter.once(() => {}, () => {throw new Error('We are just stringifying functions')});
        testEventEmitter.once(event, i => done());
        testEventEmitter.emit(event);
    });
    it('Can emit SymbolStore children', (done) => {
        const event = testSymbolStore;
        testEventEmitter.once(event, i => done());
        testEventEmitter.emit(event.item('child'));
    });
    it(`Can emit SymbolStore children's children`, (done) => {
        const event = testSymbolStore;
        testEventEmitter.once(event.item('child'), i => done());
        testEventEmitter.emit(event.item('child').item('child of child'));
    });
});