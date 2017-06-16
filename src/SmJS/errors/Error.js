/**
 * @param sup
 * @constructor
 * @extends Error
 */
import SymbolStore from "../std/symbols/SymbolStore";
export class GenericError extends Error {
    constructor(message, symbol) {
        
        let symbolString;
        
        if (typeof symbol instanceof SymbolStore) symbolString = symbol.symbolName;
        else if (typeof symbol === "symbol") symbolString = symbol.toString();
        else symbolString = symbol;
        
        if (typeof message === 'string' && typeof symbolString === 'string') {
            message += ` (acting on Sym[ ${symbolString} ])`;
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