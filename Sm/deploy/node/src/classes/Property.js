import {types as known_type_array} from "../entities/";

export default class Property {
    constructor(name, obj) {
        this.inherit(obj);
        this._name = name;
    }

    inherit(obj) {
        for (let _prop in obj) {
            if (!obj.hasOwnProperty(_prop)) continue;
            this[_prop] = obj[_prop];
        }
        this._types      = this._types || [];
        this._length     = this._length || 0;
        this._is_unique  = !!this._is_unique;
        this._is_primary = !!this._is_primary;
    }

    set len(l) {
        l            = parseInt(l);
        this._length = l < 0 ? 0 : l;
    }

    set primary(primary) {
        this._is_primary = !!primary;
    }

    set unique(unique) {
        this._is_unique = !!unique;
    }

    set types(types) {
        types = Array.isArray(types) ? types : [];

        //loop through to make sure we know of every type that we are referring to
        for (let i = 0; i < types.length; i++) {
            let t = types[i];
            if ((known_type_array.indexOf(t) < 0)) {
                throw new Error(`Cannot identify ${t}`);
            }
        }

        this._types = types;
    }
}
