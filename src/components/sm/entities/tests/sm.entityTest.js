import {describe, it} from "mocha";
import {expect} from "chai";
import {models} from './models'
import ModelConfiguration from "../model/configuration";
import {Model} from "../model/model";
import entities from './entities'
import {Entity} from "../entity/entity";
import EntityConfiguration from "../entity/configuration";
import './_debug'

let getConfigPromises = function (configurables, entityTypes, allModels, doLog = false) {
    return Object.entries(configurables)
                 .map(([model_name, config]) => {
                     const [SmEntity, SmEntityConfiguration] = entityTypes;
                     const sm_entity                         = new SmEntity;
                     const configuration                     = new SmEntityConfiguration(config);
                     const configPromise                     = configuration.configure(sm_entity)
                                                                            .catch(e => console.log(e))
                                                                            .then(configuredObject => {
                                                                                doLog && console.log(JSON.stringify(config, ' ', 4));
                                                                                doLog && console.log(JSON.stringify(configuredObject, ' ', 4));
                                                                                doLog && console.log('\n\n');
                                                                                return configuredObject;
                                                                            })
                                                                            .catch(e => console.log(e));
        
                     allModels.push(sm_entity);
                     return configPromise;
                 });
};
describe('models', () => {
    const expectedModelNames = [
        '_',
        'project',
        'user',
        'password',
        'person',
        'person_email_map',
        'email'
    ];
    
    it('Has the expected models', () => {
        expectedModelNames.forEach(key => expect(Object.keys(models)).to.contain(key));
        const configuredSmEntities = [];
        const allPromises          = getConfigPromises(models,
                                                       [Model, ModelConfiguration],
                                                       configuredSmEntities, false);
        setTimeout(() => console.log(JSON.stringify(configuredSmEntities, ' ', 4)), 1900);
        return Promise.all(allPromises);
    });
    it('Has the expected entities', () => {
        ['person', 'user', 'password'].forEach(key => expect(Object.keys(entities)).to.contain(key));
        const configurables        = entities;
        const configuredSmEntities = [];
        return Promise.all(getConfigPromises(models,
                                             [Model, ModelConfiguration],
                                             [],
                                             false))
                      .then(allModels => {
                          const allPromises = getConfigPromises(configurables,
                                                                [Entity, EntityConfiguration],
                                                                configuredSmEntities,
                                                                false);
                          setTimeout(() => console.log(JSON.stringify(configuredSmEntities, ' ', 4)), 1900);
                          return Promise.all(allPromises);
                      });
        
    });
});