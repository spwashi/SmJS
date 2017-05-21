const _to_string = name => {
    const name_type = typeof name;
    let str;
    if (name_type === 'string') return name;
    if (name_type === 'symbol') {
        return '<' + String(name).slice(7, -1) + '>';
    }
    if (name_type === 'object') return String(name);
    throw new Error(['Cannot handle this type --' + typeof name]);
};

/**
 * Standard SymbolStore
 * @type {Symbol}
 */
const $_$ = Symbol.for('$_$');

/**
 * Class meant to handle a sort of unique ID system using symbols
 * @class SymbolStore
 */
class SymbolStore {
    /**
     * @name SymbolStore.constructor
     * @param name
     * @param parent
     * @param symbol
     */
    constructor(name, parent = null, symbol = null) {
        let parent_name;
        if (parent instanceof SymbolStore) parent_name = parent.name;
    
        const _new_name = (parent_name ? _to_string(parent_name) + '.' : '') + _to_string(name);
        this._name      = _new_name;
        this._Symbol    = symbol || Symbol(_new_name);
        
        this._items = {};
        /** @type {Set<Symbol>}  */
        this._family = new Set;
        SymbolStore._registry[this._Symbol] = this;
        this._parent                        = null;
        /** @type {SymbolStore}  */
        let originSymbolStore;
        // If we are adding a symbol that exists in the registry, add its family to this one
        if (typeof name === 'symbol' && (originSymbolStore = SymbolStore.find(name))) {
            this._origin = originSymbolStore;
            this._family = new Set([...this.family, ...originSymbolStore.family, originSymbolStore.Symbol])
        }
        if (parent && parent instanceof SymbolStore) {
            this._parent = parent;
            this._family = new Set([...parent.family, parent.Symbol, ...this.family])
        }
    }
    
    get origin() {
        return this._origin || this;
    }
    
    /**
     * @return {SymbolStore|boolean}
     */
    static get $_$() {
        return this.find($_$);
    }
    
    /**
     * @alias  SymbolStore.constructor
     */
    static init(name) {
        return this.find(name) || new SymbolStore(...arguments);
    }
    
    get items() {
        return this._items;
    }
    
    /**
     *
     * @return {Set}
     */
    get family() {
        return this._family;
    }
    
    get parent() {
        return this._parent;
    }
    
    get name() {
        return this._name;
    }
    
    get Symbol() {
        return this._Symbol;
    }
    
    /**
     *
     * @param item
     * @return {SymbolStore|boolean}
     */
    static find(item) {
        if (typeof  item !== 'symbol') return false;
        return this._registry[item] || false;
    }
    
    /**
     *
     * @param {string|SymbolStore|Symbol}   name
     * @return {SymbolStore}
     */
    item(name) {
        if (name instanceof SymbolStore) name = name.Symbol;
        if (this._items[name]) return this._items[name];
        const symbolStore = new SymbolStore(name, this);
        this._items[name] = symbolStore;
        return symbolStore;
    }
    
    get_NAME_(name) {
        if (this.Symbol === $_$) return this.item(name);
        return this.item(SymbolStore.find($_$).item(name))
    }
    
    /** @return {SymbolStore}*/
    get STATIC() { return this.get_NAME_('STATIC'); }
    
    /** @return {SymbolStore}*/
    get ERROR() { return this.get_NAME_('ERROR'); }
    
    /** @return {SymbolStore}*/
    get BEGIN() { return this.get_NAME_('BEGIN'); }
    
    /** @return {SymbolStore}*/
    get COMPLETE() { return this.get_NAME_('COMPLETE'); }
    
    /** @return {SymbolStore}*/
    get CANCEL() { return this.get_NAME_('CANCEL'); }
    
}
SymbolStore._registry = {};

new SymbolStore($_$, null, $_$);

export default SymbolStore;
export {SymbolStore};