/**
 * Created by Sam Washington on 5/6/17.
 */
import {SYMBOL, symbolModifiers} from "./index";
export default class SymbolRegistrator {
    static get _modifiers() {
        return Object.values(symbolModifiers);
    }
    
    get _modifiers() {
        return this.constructor._modifiers;
    }
    
    /**
     *
     * @param {string|Array}symbol_name
     * @return {SymbolRegistrator}
     */
    register(symbol_name) {
        if (Array.isArray(symbol_name)) {
            symbol_name.forEach(name => this.register(name));
            return this;
        }
        let _parent_name       = this._name ? `${this._name}.` : '';
        let symbol_name_string = typeof symbol_name === 'symbol' ? symbol_name.toString() : symbol_name;
        let _name              = `${_parent_name}${symbol_name_string}`;
        
        this[symbol_name] = this[symbol_name] || SymbolRegistrator.init(Symbol(_name),
                                                                        this,
                                                                        _name,
                                                                        symbol_name);
        this._items.push(this[symbol_name]);
        return this;
    }
    
    item(symbol_name) {
        if (this[symbol_name]) return this[symbol_name];
        return this.register(symbol_name)[symbol_name];
    }
    
    constructor(symbol, _parent, alias, original_name) {
        this._items                  = [];
        this._name                   = alias || null;
        this._original_name          = original_name || alias || null;
        this[SYMBOL]                 = symbol;
        SymbolRegistrator[symbol]    = this;
        this._parent                 = _parent && _parent instanceof SymbolRegistrator ? _parent : null;
        const _symbol_modifier_array = this.constructor._modifiers;
        if (typeof original_name !== 'symbol' || _symbol_modifier_array.indexOf(original_name) < 0) {
            this.createModifiers();
        }
    }
    
    toString() { return this[SYMBOL].toString(); }
    
    static init(...args) { return new this(...args); }
    
    createModifiers() {
        this._modifiers.forEach(symbol_name => this.register(symbol_name));
    }
}