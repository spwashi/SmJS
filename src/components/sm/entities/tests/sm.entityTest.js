import {describe, it} from "mocha";
import {expect} from "chai";
import * as models from './models'
import ModelConfiguration from "../model/configuration";
import {Model} from "../model/model";

describe('models', () => {
    const expectedModelNames = [
        '_',
        'contact',
        'user_account_map',
        'user',
        'person',
        'person_email_map',
        'email'
    ];
    
    it('Has the expected models', () => {
        expectedModelNames.forEach(key => expect(Object.keys(models)).to.contain(key));
        
        const allPromises = [];
        const allModels   = [];
        
        Object.entries(models)
              .forEach(([model_name, model_config]) => {
                  console.log([model_name, model_config]);
                  const model         = new Model;
                  const configPromise =
                            (new ModelConfiguration(model_config)).configure(model)
                                                                  .then(configuredModel => {
                    
                                                                      console.log(JSON.stringify(model_config, ' ', 4));
                                                                      console.log(JSON.stringify(configuredModel, ' ', 4));
                                                                      console.log('\n\n');
                    
                                                                      return configuredModel;
                                                                  });
                  allModels.push(model);
                  allPromises.push(configPromise);
              });
        setTimeout(() => console.log(JSON.stringify(allModels, ' ', 4)), 1900);
        return Promise.all(allPromises);
    });
});