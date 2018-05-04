import {INTEGER_, NULL_, STRING_} from "../datatypes";
import * as _ from "../_";
import {person__identity} from "../person";
import {Model} from "../helpers";

export const name              = 'project';
export const project__identity = Model.identify(name);
export const inherits          = _.name;
export const properties        = {
    name:          {
        length:    255,
        datatypes: [STRING_],
    },
    external_link: {
        length:    255,
        datatypes: [STRING_, NULL_],
        formatAs:  'url'
    },
    price:         {
        length:    11,
        datatypes: [STRING_, NULL_],
        formatAs:  'price'
    },
    description:   {
        length:    1000,
        datatypes: [STRING_, NULL_],
    },
    person_id:     {
        datatypes: [INTEGER_, NULL_],
        reference: {
            identity:        person__identity,
            hydrationMethod: {
                property: 'id'
            }
        }
    }
};