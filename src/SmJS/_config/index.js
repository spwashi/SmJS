/**
 * Created by Sam Washington on 4/23/17.
 */
import * as _defaults from "./_defaults";
export let types   = [..._defaults.types];
export let models  = Object.assign({},
                                   _defaults.models,
                                   {
                                       _sm_entity:       {
                                           inherits: ['[Model]_entity'],
                                           source:   '_'
                                       },
                                       _sm_relationship: {
                                           inherits: ['[Model]_sm_entity', '[Model]_relationship']
                                       },
    
                                       entities:                  {inherits: ['[Model]_sm_entity'], properties: {name: {inherits: ['[Model]_{item_name}']}}},
                                       entity_classes:            {inherits: ['[Model]_sm_entity']},
                                       properties:                {
                                           inherits:   ['[Model]_sm_entity'],
                                           properties: {value: {type: 'INT'}}
                                       },
                                       types:                     {
                                           inherits:   ['[Model]_sm_entity'],
                                           properties: {parent_type: {inherits: ['[Model]types|id',]}}
                                       },
                                       entity_class_property_map: {
                                           inherits:   ['[Model]_sm_relationship'],
                                           properties: {
                                               entity_class_id: {inherits: ['[Model]entity_classes|id',]},
                                               property_id:     {inherits: ['[Model]properties|id',]}
                                           }
                                       },
                                       property_type_map:         {
                                           inherits:   ['[Model]_sm_relationship'],
                                           properties: {
                                               property_id: {inherits: ['[Model]properties|id',]},
                                               type_id:     {inherits: ['[Model]types|id',]}
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

export default {models, sources};