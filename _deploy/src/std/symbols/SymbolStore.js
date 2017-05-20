const _to_string = name => {
    const name_type = typeof name;
    if (name_type === 'string') return name;
    if (name_type === 'symbol' || name_type === 'object') return name_type.toString();
    throw new Error(['Cannot handle this type --' + typeof name]);
};

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
        this._name   = name;
        this._Symbol = symbol || Symbol(_to_string(name));
        
        this._items = {};
        /** @type {Set<Symbol>}  */
        this._family = new Set;
        SymbolStore._registry[this._Symbol] = this;
        this._parent                        = null;
        /** @type {SymbolStore}  */
        let originSymbolStore;
        // If we are adding a symbol that exists in the registry, add its family to this one
        if (typeof name === 'symbol' && (originSymbolStore = SymbolStore.find(name))) {
            this._family = new Set([...this.family, ...originSymbolStore.family, originSymbolStore.Symbol])
        }
        if (parent && parent instanceof SymbolStore) {
            this._parent = parent;
            this._family = new Set([...parent.family, parent.Symbol, ...this.family])
        }
    }
    
    /**
     * @alias  SymbolStore.constructor
     */
    static init() {
        return new SymbolStore(...arguments);
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
}
SymbolStore._registry = {};
export default SymbolStore;
export {SymbolStore};