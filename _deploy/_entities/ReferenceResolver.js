/**
 * Created by Sam Washington on 5/4/17.
 */
import Std from "./Std";
import {EVENTS} from "./events/index";
import {SYMBOL, symbols} from "./symbols/index";
class ReferenceResolver extends Std {
    
    constructor(_config = {}) {
        super(_config);
        let _$Events$ = this.static[EVENTS];
        
        _config.follows = _config.follows || [];
        if (!_config.follows.length) {
            this.Events.emitOnce(_$Events$.inherit.references[symbols.modifiers.COMPLETE][SYMBOL], this);
            delete  _config.follows;
        }
        
        this.Events.bindEvent([_$Events$.init.self[SYMBOL], _$Events$.complete.self[SYMBOL]],
                              this.static.Events);
    }
    
    set follows(follows) {
        this._inheritReferences(follows)
            .then(() => {
                this.Events.emitOnce(this.static[EVENTS].inherit.references[symbols.modifiers.COMPLETE][SYMBOL], this)
            });
    }
    
    _inheritReferences(references) {
        this.Events.emitOnce(this.static[EVENTS]
                                 .inherit
                                 .references
                                 [symbols.modifiers.BEGIN]
                                 [SYMBOL],
                             this);
        
        if (typeof references === "string") references = [references];
        else if (!Array.isArray(references)) references = [];
        
        if (!references.length) return Promise.resolve();
        
        let _Promise = Promise.resolve();
        
        references.forEach(reference_identifier => {
            _Promise =
                _Promise
                    .then(_ => {
                        return this._inheritReference(reference_identifier)
                    });
        });
        return _Promise;
    }
    
    static _initClass() {
        if (super._initClass() === null) return null;
        ReferenceResolver.ReferenceClasses = ReferenceResolver.ReferenceClasses || new Map;
        ReferenceResolver.ReferenceClasses.set(this._object_type, this);
        
        this.static._references = this.static._references || new Map;
        ReferenceResolver.Events.emitOnce(this.static[EVENTS]
                                              .init
                                              ._class
                                              .item(Symbol.for(`object#${this._object_type}`))
                                              [SYMBOL],
                                          this);
        return true;
    }
    
    static when_class(object_name) {
        return ReferenceResolver.Events.when(this.static[EVENTS]
                                                 .init
                                                 ._class
                                                 .item(Symbol.for(`object#${object_name}`))
                                                 [SYMBOL]);
    }
    
    static when_instance(identifier) {
        return Promise.race([this.Events.when(this.static[EVENTS].init.self[SYMBOL],
                                              (...arg) => {
                                                  return arg[0] instanceof Std && arg[0]._identity === identifier;
                                              })],)
    }
    
    _inheritReference(reference_identifier) {
        // assume the identifier looks like  [object_name|identifier]{index}
        let [, object_name, identifier, index] = reference_identifier.match(/\[([a-zA-Z]+)\|([a-zA-Z_]+)](?:({[a-zA-Z_]+}))?/);
        if (!identifier || !identifier.length) identifier = '_';
        
        this.Events.emit(this.static[EVENTS].inherit.references[symbols.modifiers.STEP][SYMBOL], this, reference_identifier);
        
        return ReferenceResolver.when_class(object_name)
                                .then((ReferenceClass) => {
                                    return ReferenceClass.resolve(identifier);
                                })
                                .then((_reference) => {
                                    return index ? _reference.resolve(index) : _reference.complete;
                                })
                                .then((_reference) => {
                                    if (!(_reference instanceof ReferenceResolver)) throw new Error('Cannot resolve reference' + JSON.stringify(_reference));
                                    return _reference;
                                })
                                .then(/**@param _reference {ReferenceResolver} */
                                      (_reference) => {
                                          this.inherit(_reference);
                                          return _reference;
                                      }
                                );
    }
    
    static resolve(name) {
        return this.when_instance(name);
    }
    
    resolve(name) {
        return Promise.reject(`No property for ${name}`);
    }
}
export default ReferenceResolver;