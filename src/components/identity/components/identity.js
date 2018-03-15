// @flow
import type {identifier, IdentityNode} from "../types";
import {errors} from "../constants/index";
import {createName} from "../../sm/identification";

/**
 * We only really want to initialize Identities through their respective IdentityManager
 * @type {Symbol}
 * @private
 */
const _PRIVATE_    = Symbol('private');
const initIdentity = (identifier, identityManager): Identity => new Identity(_PRIVATE_, identifier, identityManager);

class IdentityManager implements IdentityNode {
    _identifier: identifier;
    
    constructor(identifier: identifier) {
        this._identifier = identifier;
    }
    
    instance(initialIdentifier: identifier | string | Identity): Identity {
        if (initialIdentifier instanceof Identity) initialIdentifier = initialIdentifier.identifier;
        const identifier = createName.asInstance(this._identifier, createName(initialIdentifier));
        return initIdentity(identifier, this);
    }
    
    component(initialIdentifier: identifier | string | Identity): Identity {
        if (initialIdentifier instanceof Identity) initialIdentifier = initialIdentifier.identifier;
        const identifier = createName.asComponent(this._identifier, createName(initialIdentifier));
        return initIdentity(identifier, this);
    }
    
    identityFor(initialIdentifier: identifier | string | Identity) {
        if (initialIdentifier instanceof Identity) initialIdentifier = initialIdentifier.identifier;
        const identifier = createName.ofType(this._identifier, initialIdentifier);
        return initIdentity(identifier, this);
    }
}

export default class Identity implements IdentityNode {
    _identifier: identifier;
    _identityManager: IdentityManager;
    _parent: Identity;
    
    constructor(_private: typeof _PRIVATE_, identifier: identifier, identityManager: IdentityManager) {
        if (_private !== _PRIVATE_) {
            throw new Error(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
        }
        
        this._identifier = identifier;
        identityManager && this._setIdentityManager(identityManager);
    }
    
    get identifier(): identifier {
        return this._identifier;
    }
    
    component(name: identifier | Identity): Identity {
        if (name instanceof Identity) name = name.identifier;
        const identifier = createName.asComponent(this._identifier, name);
        return initIdentity(identifier)._setParent(this);
    }
    
    instance(name: identifier | Identity): Identity {
        if (name instanceof Identity) name = name.identifier;
        const identifier = createName.asInstance(this._identifier, name);
        return initIdentity(identifier)._setParent(this);
    }
    
    _setParent(parent: Identity) {
        this._parent = parent;
        return this;
    }
    
    _setIdentityManager(identityManager: IdentityManager) {
        this._identityManager = identityManager;
        return this;
    }
    
    toJSON() {
        return this._identifier
    }
}

export const createIdentityManager = (identifier: identifier): IdentityManager => new IdentityManager(identifier);