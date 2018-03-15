import {Model} from "../../../model/model";
import {INTEGER_, STRING_} from "../datatypes";

export const email__identity = Model.identify('emails');
export const inherits        = '_';
export const properties      = {
    id:    {length: 11, datatypes: [INTEGER_]},
    email: {length: 255, datatypes: [STRING_], unique: true},
};
export const contexts        = {
    "*_email_map": {
        roleName: 'email'
    }
};