import {describe, it} from "mocha";
import {Sm} from "../Sm";

describe('EventEmitter', () => {
    const EventEmitter     = Sm.std.Emitter;
    const testEventEmitter = new EventEmitter;
    
    it('Can emit strings', done => {
        const event = 'test';
        testEventEmitter.on(event, i => done());
        testEventEmitter.emit(event);
    });
    
    it('Can emit symbols', done => {
        const event = Symbol('test');
        testEventEmitter.on(event, i => done());
        testEventEmitter.emit(event);
    });
    
    const SymbolStore     = Sm.std.symbols.SymbolStore;
    /** @type {SymbolStore}  */
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
        testEventEmitter.emit(event.instance('child'));
    });
    it(`Can emit SymbolStore children's children`, (done) => {
        const event = testSymbolStore;
        testEventEmitter.once(event.instance('child'), i => done());
        testEventEmitter.emit(event.instance('child').instance('child of child'));
    });
    // Emit SymbolStores and know that we've done it already
    it(`Can emit 'static' symbol stores`, done => {
        const staticEvent = testSymbolStore.instance('test_static').STATIC;
        testEventEmitter.emit(staticEvent);
        testEventEmitter.once(testSymbolStore.instance('test_static'), i => done())
    });
});