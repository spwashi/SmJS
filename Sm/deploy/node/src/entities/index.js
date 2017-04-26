/**
 * Created by Sam Washington on 4/23/17.
 */
let model_properties = {
    id:          {
        types:   ['int'],
        len:     11,
        primary: true
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
};

let known_type_array = [
    'string',
    'int',
    'datetime'
];

module.exports = {
    types:  [...known_type_array],
    models: {
        _:                   {
            properties: model_properties,
            src:        {table: '#', database: 'test'}
        },
        types:               {
            follows:    ['{model=entity}'],
            properties: {
                parent_type: {follows: '{model=types}.id'}
            }
        },
        entity_classes:      {
            follows: ['{model=entity}']
        },
        properties:          {
            follows: ['{model=entity}']
        },
        entity_property_map: {
            follows:    ['{model=relationships}'],
            properties: {
                entity_id:   {follows: '{model=entity_classes}.id'},
                property_id: {follows: '{model=properties}.id'}
            }
        },
        property_type_map:   {
            follows:    ['{model=relationships}'],
            properties: {
                property_id: {follows: '{model=properties}.id'},
                type_id:     {follows: '{model=types}.id'}
            }
        }
    },
};