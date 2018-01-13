import Identity from "./components/identity";

export type identifier = string;

export interface Identifiable {
    +IDENTITY: Identity
}

export interface IdentityManager {
    static resolve(identity: identifier): Identifiable;
}