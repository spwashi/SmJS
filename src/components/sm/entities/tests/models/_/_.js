import {Model} from "../../../model/model";
import {INTEGER_} from "../datatypes";

export const name        = '_';
export const properties  = {
    id: {
        length:    11,
        datatypes: [INTEGER_],
        primary:   true,
    }
};
export const ___identity = Model.identify(name);
