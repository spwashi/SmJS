import Identity from "./components/identity";

export type identifier = string;

export interface Identifier {
    static identify(name: string): Identity;
}
export interface Identifiable {
    +IDENTITY: Identity
}

export interface IdentityNode {
    instance(identifier: identifier | Identity): IdentityNode;
    
    component(identifier: identifier | Identity): IdentityNode;
}

export interface IdentityManager {
    static resolve(identity: identifier): Identifiable;
}