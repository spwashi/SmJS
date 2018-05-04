import {user__identity} from "../../models/user";
import {CONTEXT_TYPES, normalizeContexts} from "../helpers";
import * as password from '../password'
import {Entity} from "../helpers";

const getContexts              = () => {
    const user_context   = {
        self: {
            name:        'user',
            title:       'User',
            contextType: CONTEXT_TYPES.IDENTITY
        },
    };
    const login_process  = {...user_context};
    const signup_process = {...user_context};
    return normalizeContexts({
                                 user_context,
                                 login_process,
                                 signup_process
        
                             })
};
export const name              = 'user';
export const contexts          = getContexts();
export const identity          = Entity.identify(name);
export const persistedIdentity = user__identity;
export const properties        = {
    username: true,
    email:    {
        identity: true
    },
    password: {
        // This property is derived by joining on the password entity's persisted identity injecting this as the user_id
        derivedFrom: {user_id: true},
        identity:    password.identity,
        contexts:    [
            contexts.login_process,
            contexts.signup_process
        ]
    }
};
