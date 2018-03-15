import {Model} from "../../../model/model";

export const user_account_map__identity = Model.identify('user_account_map');

const INTEGER_ = 'int';
export const inherits   = '_';
export const name       = 'user_account_map';
export const map        = {
    owner_id:   '{owner}id',
    account_id: '{account}id'
};
export const properties = {
    user_role: {length: 11, datatypes: [INTEGER_]},
};