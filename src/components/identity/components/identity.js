// @flow
import type {identifier} from "../types";
import {errors} from "../constants/index";
import {createName} from "../../sm/identification";

const _PRIVATE_ = Symbol('private');

interface IdentityNode {
    item(identifier: identifier | Identity): IdentityNode;
    
    component(identifier: identifier | Identity): IdentityNode;
}

let createIdentityProxy = function (instance) {
    const canUseDynamicItem = property => typeof property === 'string';
    return new Proxy(instance, {
        get: (target: Object | IdentityNode, property) => {
            if ((target: Object)[property]) return (target: Object)[property];
            if (!canUseDynamicItem(property)) return;
            
            return target.component(property);
        }
    })
};

class IdentityManager implements IdentityNode {
    _identifier: identifier;
    
    constructor(identifier: identifier) {
        this._identifier = identifier;
    }
    
    item(initialIdentifier: identifier | string | Identity): Identity {
        if (initialIdentifier instanceof Identity) initialIdentifier = initialIdentifier.identifier;
        const identifier = createName.asChild(this._identifier, createName(initialIdentifier));
        const instance   = new Identity(_PRIVATE_, identifier, this);
        
        return createIdentityProxy(instance);
    }
    
    component(initialIdentifier: identifier | string | Identity): Identity {
        if (initialIdentifier instanceof Identity) initialIdentifier = initialIdentifier.identifier;
        const identifier = createName.asComponent(this._identifier, createName(initialIdentifier));
        const instance   = new Identity(_PRIVATE_, identifier, this);
        return createIdentityProxy(instance);
    }
    
    create(initialIdentifier: identifier | string | Identity) {
        if (initialIdentifier instanceof Identity) initialIdentifier = initialIdentifier.identifier;
        const identifier = createName.ofType(this._identifier, createName(initialIdentifier));
        const instance   = new Identity(_PRIVATE_, identifier, this);
        
        return createIdentityProxy(instance);
    }
    
}

export const createIdentityManager = (identifier: identifier): IdentityManager => new IdentityManager(identifier);
export default class Identity implements IdentityNode {
    _identifier: identifier;
    _identityManager: IdentityManager;
    _parent: Identity;
    
    constructor(_private: typeof _PRIVATE_, identifier: identifier, identityManager: IdentityManager) {
        if (_private !== _PRIVATE_) {
            throw new Error(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
        }
        
        this._identifier = identifier;
        this._setIdentityManager(identityManager);
    }
    
    get identifier(): identifier {
        return this._identifier;
    }
    
    component(name: identifier | Identity): Identity {
        if (name instanceof Identity) name = name.identifier;
        const parent_id  = this._identifier;
        const identifier = createName.asComponent(parent_id, name);
        
        const identity = (new Identity(_PRIVATE_, identifier))._setParent(this);
        return createIdentityProxy(identity);
    }
    
    item(name: identifier | Identity): Identity {
        if (name instanceof Identity) name = name.identifier;
        const parent_id  = this._identifier;
        const identifier = createName.asChild(parent_id, name);
        
        const identity = (new Identity(_PRIVATE_, identifier))._setParent(this);
        return createIdentityProxy(identity);
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