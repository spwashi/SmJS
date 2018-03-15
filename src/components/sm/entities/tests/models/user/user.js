import {Model} from "../../../model/model";
import {INTEGER_, NULL_, STRING_} from "../datatypes";
import {person__identity} from "../person/person";
import {email__identity} from "../email/email";

export const name     = 'users';
export const inherits = '_';

export const properties = {
    person_id:  {
        length:    11,
        datatypes: [INTEGER_],
        unique:    true,
        
        proxy: {
            roleName: 'person',
            identity: [
                person__identity,
                {'.': 'id'}
            ]
        }
    },
    email:      {
        length:    255,
        datatypes: [STRING_],
        unique:    true,
        
        cast: [
            {
                identity: [
                    email__identity,
                    {'.': 'email'}
                ],
            }
        ]
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
