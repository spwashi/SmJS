/**
 * Created by Sam Washington on 5/2/17.
 */

export let models = {
    _:             {
        properties: {
            id:          {
                types:   ['int'],
                len:     11,
                primary: true,
                unique:  true,
                // properties that don't get inherited
                exclude: ['primary', 'unique']
            },
            item_name:   {
                types:  ['string'],
                len:    45,
                unique: true
            },
            person_name: {
                types: ['string'],
                len:   35
            },
            timestamp:   {
                types: ['datetime']
            }
        },
        src:        {table: '#', database: 'test'}
    },
    _entity:       {
        properties: {
            id:             {
                inherits: '[Model]_|id',
                primary:  true,
                unique:   true,
                exclude:  ['primary', 'unique']
            },
            creation_dt:    {inherits: '[Model]_|timestamp', _default: '__NOW__'},
            last_update_dt: {inherits: '[Model]_|timestamp'}
        }
    },
    _inheriting:   {
        properties: {
            id: {inherits: '[Model]_entity|id', primary: false, unique: false}
        }
    },
    _relationship: {
        inherits:   ['[Model]_entity'],
        properties: {
            id: {
                inherits: '[Model]_entity|id',
                primary:  true,
                unique:   true,
            },
        }
    },
};
export let types  = [
    'string',
    'int',
    'datetime'
];