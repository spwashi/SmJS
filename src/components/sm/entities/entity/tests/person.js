import {Model} from "../../model/model";

export const components = {
    user:     {
        model: Model.identify('users'),
    },
    contact:   {
        model:   Model.identify('contacts'),
        context: {
            serviceProvider: Model.identify('users')
        }
    },
    employee: {
        model:   Model.identify('employees'),
        context: {
            company: Model.identify('companies')
        },
    }
};

export const properties = {
    username:        {
        index:       true,
        datatypes:   ['string'],
        derivedFrom: components.user,
    },
    first_name:      {
        index:       true,
        datatypes:   ['string'],
        derivedFrom: components.user,
    },
    last_name:       {
        index:       true,
        datatypes:   ['string'],
        derivedFrom: components.user,
    },
    email_addresses: {
        propertyType: 'container',
        derivedFrom:  {
            user:     'email_address',
            employee: 'email_address',
        }
    },
};