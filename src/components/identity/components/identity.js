// @flow
import type {identifier} from "../types";
import {errors} from "../constants/index";

const _PRIVATE_ = Symbol('private');

interface IdentityNode {
    item(identifier: identifier): IdentityNode;
}

class IdentityManager implements IdentityNode {
    _identifier: identifier;
    
    constructor(identifier: identifier) {
        this._identifier = identifier;
    }
    
    _createIdentifier(identifier: identifier | string) {
        if (identifier[0] === '[') identifier = '(' + identifier + ')';
        return identifier;
    }
    
    item(initialIdentifier: identifier | string): Identity {
        const instance = new Identity(_PRIVATE_, this._createIdentifier(initialIdentifier), this);
        
        return new Proxy(instance, {
            get: (target: Object | IdentityNode, property) => {
                if ((target: Object)[property]) return (target: Object)[property];
                if (!this.canUseDynamicItem(property)) return;
                
                return target.item(property);
            }
        })
    }
    
    canUseDynamicItem(property: any) {
        return typeof property === 'string';
    }
}

export const createIdentityManager = (identifier: identifier) => new IdentityManager(identifier);
export const create_identifier     = (name: identifier | string | Symbol): identifier => name;
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
    
    item(name: identifier): Identity {
        
        //todo types of identifiers?
        const identifier = this._identifier + ' ' + name;
        
        return this._identityManager.item(identifier)._setParent(this);
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
        return {
            name: this._identifier
        }
    }
}