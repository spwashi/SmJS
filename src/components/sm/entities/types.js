// @flow

import {Identifiable, identifier, IdentityManager} from "../../identity/types";

export interface SmEntity extends Identifiable {
    static resolve(identity: identifier): SmEntity;
}