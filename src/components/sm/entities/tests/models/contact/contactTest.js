import {before, beforeEach, describe, it} from "mocha";
import {expect} from "chai";
import * as __modelConfig from '../_/_';
import * as person_modelConfig from '../person/person';
import * as contact_modelConfig from '../contact/contact';
import ModelConfiguration from "../../../model/configuration";
import {Model} from "../../../model/model";

let initializeUnderscoreModel = () => (new ModelConfiguration(__modelConfig)).configure(new Model);

describe('Contact Model', () => {
    it(`(looks like it's supposed to)`, done => {
        initializeUnderscoreModel();
        const modelConfiguration = new ModelConfiguration(contact_modelConfig);
        modelConfiguration.configure(new Model)
                          .then(ContactModel => {
                              console.log(JSON.stringify(ContactModel, ' ', 3));
                              done();
                          })
    });
    
    it('Has a  "person_id" property that serves allows us to proxy a "Person" Model', done => {
        initializeUnderscoreModel();
        (new ModelConfiguration(person_modelConfig)).configure(new Model);
        (new ModelConfiguration(contact_modelConfig)).configure(new Model)
                                                     .then(ContactModel => {
                                                         const json__string = JSON.stringify(ContactModel, ' ', 3);
                                                         console.log(json__string);
                                                         done();
                                                     });
    });
});