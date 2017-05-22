/**
 * Created by Sam Washington on 4/23/17.
 */
import * as _defaults from "./_defaults";
export let types   = [..._defaults.types];
export let models  = Object.assign({},
                                   _defaults.models,
                                   {
                                       _sm_entity:       {follows: ['[Model]_entity'], source: '_'},
                                       _sm_relationship: {follows: ['[Model]_sm_entity', '[Model]_relationship']},
    
                                       entities:                  {follows: ['[Model]_sm_entity'], properties: {name: {follows: ['[Model]_{item_name}']}}},
                                       entity_classes:            {follows: ['[Model]_sm_entity']},
                                       properties:                {follows: ['[Model]_sm_entity']},
                                       types:                     {
                                           follows:    ['[Model]_sm_entity'],
                                           properties: {parent_type: {follows: ['[Model]types|id',]}}
                                       },
                                       entity_class_property_map: {
                                           follows:    ['[Model]_sm_relationship'],
                                           properties: {
                                               entity_class_id: {follows: ['[Model]entity_classes|id',]},
                                               property_id:     {follows: ['[Model]properties|id',]}
                                           }
                                       },
                                       property_type_map:         {
                                           follows:    ['[Model]_sm_relationship'],
                                           properties: {
                                               property_id: {follows: ['[Model]properties|id',]},
                                               type_id:     {follows: ['[Model]types|id',]}
                                           }
                                       }
                                   });
export let sources = {
    _factshift_config: {
        name:    'FactshiftConfig',
        type:    'database',
        details: {}
    },
    _:                 {
        name:    (item) => {item._name},
        type:    'table',
        details: {database: '[source|_factshift_config]'}
    }
};
