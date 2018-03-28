import {Model} from "../../../model/model";
import {INTEGER_, NULL_, STRING_} from "../datatypes";
import {person__identity} from "../person/index";
import {email__identity} from "../email/index";

export const name     = 'users';
export const inherits = '_';

export const properties = {
    person_id:  {
        length:    11,
        datatypes: [INTEGER_],
        unique:    true,
    },
    email:      {
        length:    255,
        datatypes: [STRING_],
        unique:    true,
    },
    first_name: {
        length:    50,
        datatypes: [STRING_, NULL_]
    },
    last_name:  {
        length:    50,
        datatypes: [STRING_, NULL_]
    }
};

export const user__identity = Model.identify(name);
