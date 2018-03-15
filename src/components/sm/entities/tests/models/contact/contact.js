import {Model} from "../../../model/model";
import {INTEGER_} from "../datatypes";

const person__identity = Model.identify('people');

export const name = 'contacts';

export const inherits   = '_';
export const properties = {
    person_id: {
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
};

export const contact__identity = Model.identify(name);