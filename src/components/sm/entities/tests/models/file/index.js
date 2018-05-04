import {user__identity} from "../user";
import * as _ from "../_";
import {Model} from "../helpers";

export const inherits       = _.name;
export const name           = 'file';
export const file__identity = Model.identify(name);
export const properties     = {
    user_id: {
        reference: {
            identity:        user__identity,
            hydrationMethod: {property: 'id'}
        }
    },
};