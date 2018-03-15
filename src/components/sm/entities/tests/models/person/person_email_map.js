import {Model} from "../../../model/model";

export const inherits                   = '_';
export const name                       = 'person_email_map';
export const map                        = {
    person_id: '{person}id',
    email_id:  '{email}id'
};
export const properties                 = {};
export const person_email_map__identity = Model.identify(name);