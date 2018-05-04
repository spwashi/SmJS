import {person__identity} from "../../models/person";
import {CONTEXT_TYPES, normalizeContexts} from "../helpers";
import * as user from "../user";
import {Entity} from "../helpers";

const getContexts              = () => {
    const person_context = {
        self: {
            name:        'person',
            title:       'Person',
            contextType: CONTEXT_TYPES.IDENTITY
        },
    };
    const login_process  = {...person_context};
    const signup_process = {...person_context};
    return normalizeContexts({
                                 person_context,
                                 login_process,
                                 signup_process
        
                             })
};
export const name              = 'person';
export const contexts          = getContexts();
export const identity          = Entity.identify(name);
export const persistedIdentity = person__identity;
export const properties        = {
    user: {
        derivedFrom: {person_id: true},
        identity:    user.identity,
    }
};
