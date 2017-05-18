/**
 * Created by Sam Washington on 5/4/17.
 */
import ReferenceResolver from "./ReferenceResolver";
import {types as known_type_array} from "./_config/index";
import {EVENTS} from "./events/index";
import {SYMBOL, symbols} from "./symbols/index";

/**
 * @class Property
 * @extends ReferenceResolver
 */
class Property extends ReferenceResolver {
    constructor(_config = {}) {
        super(_config);
        this.Events
            .when(this.static[EVENTS]
                      .inherit
                      .references
                      [symbols.modifiers.COMPLETE]
                      [SYMBOL])
            .then(_ => this._complete());
    }
    
    static get defaults() {
        return {
            types:   [],
            len:     null,
            unique:  false,
            primary: false,
        }
    }
    
    createIdentity(_config = {}) {
        this._types = this._types || new Set;
        super.createIdentity(_config);
        this._identity = _config._model_identity ? `${_config._model_identity}.${this._identity}` : this._identity;
    }
    
    //
    get len() {return this._length;}
    
    get primary() {return this._is_primary;}
    
    get unique() {return this._is_unique;}
    
    get types() {return this._types;}
    
    set len(l) {
        if (l === null || typeof l === "undefined") return this._length = null;
        l            = parseInt(l);
        this._length = l < 0 ? 0 : l;
    }
    
    set primary(primary) { this._is_primary = !!primary; }
    
    set unique(unique) { this._is_unique = !!unique; }
    
    set types(types) {
        types = Array.isArray(types) ? types : [];
        
        //loop through to make sure we know of every type that we are referring to
        for (let i = 0; i < types.length; i++) {
            let t = types[i];
            if ((known_type_array.indexOf(t.toLowerCase()) < 0)) {
                throw new Error(`Cannot identify ${t}`);
            }
            this._types.add(t.toUpperCase());
        }
    }
}
Property._object_type = 'property';
export default Property;