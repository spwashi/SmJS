import {errors} from "./constants";

const _PRIVATE_ = Symbol('private');

export class Identity {
    _name: string;
    _parent: Identity;
    
    constructor(_private, name, parent: Identity) {
        if (_private !== _PRIVATE_) {
            throw new Error(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
        }
        this._name   = name;
        this._parent = parent;
    }
    
    static init(name, parent: Identity = null): Identity {
        return new Identity(_PRIVATE_, name, parent)
    }
    
    item(name) {
        const instance = Identity.init(name, this);
        return new Proxy(instance, {
            get: function (target, name) {
                if (target[name]) return target[name];
                // 'target' is the object we are returning the child of
                return target.item(name);
            }
        })
    }
    
    toJSON() {
        return {
            name: this._name
        }
    }
}
