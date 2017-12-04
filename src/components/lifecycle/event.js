export class Event {
    constructor(emitter, eventName, activeSymbol, eventFamily, args) {
        this._emitter      = emitter;
        this._eventName    = eventName;
        this._activeSymbol = activeSymbol;
        this._eventFamily  = eventFamily;
        this._args         = args;
    }
    
    get args() { return this._args; }
    
    /** @return SymbolStore*/
    get eventName() {return this._eventName;}
    
    /** @return {Symbol} */
    get activeSymbol() {return this._activeSymbol;}
    
    get eventFamily() {return this._eventFamily;}
    
    get emitter() {return this._emitter;}
}

export default Event;