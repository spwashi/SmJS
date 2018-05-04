import {INTEGER_, STRING_} from "../datatypes";
import * as _ from "../_";
import {user__identity} from "../user";
import {Model} from "../helpers";

export const name               = 'password';
export const password__identity = Model.identify(name);
export const inherits           = _.name;
export const properties         = {
    password: {
        length:    255,
        datatypes: [STRING_],
    },
    user_id:  {
        length:    11,
        datatypes: [INTEGER_],
        reference: {
            identity:        user__identity,
            hydrationMethod: {
                property: 'id'
            }
        }
    }
};