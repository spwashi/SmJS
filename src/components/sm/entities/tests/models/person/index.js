import {Model} from "../../../model/model";
import {INTEGER_, STRING_} from "../datatypes";

export const name       = 'people';
export const inherits   = '_';
export const properties = {
    id:    {length: 11, datatypes: [INTEGER_]},
    email: {length: 255, datatypes: [STRING_], unique: true},
};
export const contexts   = {
    "person_*_map": {
        roleName: 'person'
    }
};

export const person__identity = Model.identify(name);