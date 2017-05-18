/**
 * Created by Sam Washington on 5/6/17.
 */
import SymbolRegistrator from "./SymbolRegistrator";
export const symbolModifiers = {
    FIRST: Symbol('FIRST'),
    BEGIN: Symbol('BEGIN'),
    STEP:  Symbol('STEP'),
    
    COMPLETE: Symbol('COMPLETE'),
    //
    ERROR:    Symbol('ERROR'),
    CANCEL:   Symbol('CANCEL'),
};
export const SYMBOL          = Symbol('SYMBOL');
export const symbols         = SymbolRegistrator.init();
symbols.modifiers            = symbolModifiers;
