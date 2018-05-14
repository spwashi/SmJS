import {password__identity} from "../../models/password";
import {STRING_} from "../../models/datatypes";
import {Entity} from "../helpers";

export const name              = 'password';
export const identity          = Entity.identify(name);
// How do we know that this entity is /this/ entity?
export const persistedIdentity = password__identity;
export const properties        = {
    password: {
        identity:  true,
        minLength: 10,
        role:      'value'
    }
};
export const representations   = {
    inline: {
        datatype:          STRING_,
        requireFormatting: true
    }
};