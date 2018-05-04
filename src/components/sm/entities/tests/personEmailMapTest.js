import ModelConfiguration from "../model/configuration";
import * as person_email_map from './models/person/person_email_map';
import {Model} from "../model/model";
import {describe, it} from "mocha";
import {expect} from "chai";

let initializeUnderscoreModel = () => (new ModelConfiguration({name: '_'})).configure(new Model);
describe('models', () => {
    
    it(`Can map model relationship roles`, () => {
        // Given a configuration similar to what's been specified in "./person_email_map.js",
        //   expect there to be a property called "mappedModelRoles"
        const expected_mappedModelRoles = {
            person_id: {
                modelRole: {
                    roleName: "person"
                },
                property:  "id"
            },
            email_id:  {
                modelRole: {
                    roleName: "email"
                },
                property:  "id"
            }
        };
        
        //-------------------------------------
        
        // Make the "_" model available for inheritance
        initializeUnderscoreModel();
        
        const modelConfiguration = new ModelConfiguration(person_email_map);
        
        modelConfiguration.configure(new Model)
                          .catch(e => console.log(e))
        
                          .then(personEmailMap => {
                              const json__modelConfiguration = JSON.stringify(personEmailMap,
                                                                              ' ',
                                                                              3);
            
                              console.log('< -- -- json__modelConfiguration -- -- >');
                              console.log(json__modelConfiguration);
                              expect(JSON.parse(json__modelConfiguration).mappedModelRoles).to.deep.equal(expected_mappedModelRoles)
                          });
        // .then(i => done());
        
        const json__raw = JSON.stringify(person_email_map,
                                         ' ',
                                         3);
        
        // --
        console.log(json__raw);
    });
    
    it(``, () => {
        const json = JSON.stringify(person_email_map,
                                    ' ',
                                    3);
        console.log(json);
    });
    
});