import {describe, it} from "mocha";
import {expect} from "chai";
import {Model} from "./model"
import ModelConfiguration from './configuration'
import {CONFIGURATION} from "../../../configuration/configuration";

describe('Model', () => {
    it('exists', () => {
        const model = new Model;
        expect(model).to.be.instanceOf(Model);
        
        console.log(model.identity)
    });
    
    it('Can configure properties', () => {
        const config = new ModelConfiguration({
                                                  properties: {
                                                      title: {
                                                          unique:    true,
                                                          datatypes: ['int', 'string']
                                                      }
                                                  }
                                              });
        config.configure(new Model)
              .then(result => {
                  console.log(JSON.stringify(result, ' ', 5));
              });
    })
});