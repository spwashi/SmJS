/**
 * Created by Sam Washington on 4/23/17.
 */
import * as _defaults from "./_defaults";
export let types = [..._defaults.types];
export default {
    sources: {
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
    },
    models:  Object.assign({},
                           _defaults.models,
                           {
                               _sm_entity:       {follows: ['[model|_entity]'], source: '_'},
                               _sm_relationship: {follows: ['[model|_sm_entity]', '[model|_relationship]']},
        
                               entities:                  {follows: ['[model|_sm_entity]'], properties: {name: {follows: ['[model|_]{item_name}']}}},
                               entity_classes:            {follows: ['[model|_sm_entity]']},
                               properties:                {follows: ['[model|_sm_entity]']},
                               types:                     {
                                   follows:    ['[model|_sm_entity]'],
                                   properties: {parent_type: {follows: ['[model|types]{id}',]}}
                               },
                               entity_class_property_map: {
                                   follows:    ['[model|_sm_relationship]'],
                                   properties: {
                                       entity_class_id: {follows: ['[model|entity_classes]{id}',]},
                                       property_id:     {follows: ['[model|properties]{id}',]}
                                   }
                               },
                               property_type_map:         {
                                   follows:    ['[model|_sm_relationship]'],
                                   properties: {
                                       property_id: {follows: ['[model|properties]{id}',]},
                                       type_id:     {follows: ['[model|types]{id}',]}
                                   }
                               }
                           }),
};