/**
 * @param sup
 * @constructor
 * @extends Error
 */
import SymbolStore from "../std/symbols/SymbolStore";
class GenericError extends Error {
    constructor(message, symbol) {
        if (typeof symbol instanceof SymbolStore) symbol = symbol.symbolName;
        else if (typeof symbol === "symbol") symbol = symbol.toString();
        
        if (typeof message === 'string' && typeof symbol === 'string') {
            message += " in " + symbol;
        }
        
        super(message);
        
        this.active_symbol = symbol;
        this.name          = this.constructor.name;
        
        this._addToStack(message);
    }
    
    _addToStack(message) {
        if (typeof Error.captureStackTrace === "function") {
            Error.captureStackTrace(this, this.constructor);
        } else {
            this.stack = (new Error(message).stack);
        }
    }
}
export default GenericError;