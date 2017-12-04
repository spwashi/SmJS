import {errors} from "./constants";

const _PRIVATE_ = Symbol('private');

export class Identity {
    constructor(_private, name, parent: Identity) {
        if (_private !== _PRIVATE_) {
            throw new Error(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
        }
    }
    
    static init(name, parent: Identity = null): Identity {
        return new Identity(_PRIVATE_, name, parent)
    }
    
    item(name) {
    
    }
}
