import {expect} from "chai"
import {describe, it} from "mocha";
import PropertyAsReferenceConfiguration from "./configuration"
import {PropertyAsReferenceDescriptor} from "./index";
import {Model} from "../../model/model";
import {ModelConfiguration} from "../../model/configuration";
import {Property} from "../property";

describe('Property As Proxy Test', () => {
    it('Exists as a class with a configuration', done => {
        const boonmanConfig = new ModelConfiguration({name: 'boonman'});
        
        boonmanConfig.configure(new Model)
                     .catch(err => console.log(err));
        
        const config = new PropertyAsReferenceConfiguration({});
        
        config.configure(new PropertyAsReferenceDescriptor)
              .then(propertyAsReferenceDescriptor => {
                  const json__string = JSON.stringify(propertyAsReferenceDescriptor, ' ', 3);
                  console.log(json__string);
                  done();
              })
    });
    it('waits for the configuration of the Proxied identity to be complete before using it', () => {
        const MODEL__NAME     = 'footballs';
        const MODEL__IDENTITY = Model.identify(MODEL__NAME);
        const footballConfig  = new ModelConfiguration({name: MODEL__NAME});
        
        footballConfig.configure(new Model).catch(err => console.log(err));
        
        const config         = new PropertyAsReferenceConfiguration({identity: MODEL__IDENTITY});
        config.smEntityProto = Model;
        
        return config.configure(new PropertyAsReferenceDescriptor)
                     .then(
                         propertyAsReferenceDescriptor => {
                             const json__string = JSON.stringify(propertyAsReferenceDescriptor, ' ', 3);
                             console.log(json__string);
                             const objFromJSON = JSON.parse(json__string);
                             expect(objFromJSON.identity).to.equal(MODEL__IDENTITY.toJSON());
                         })
    });
    it('Can verify that the configured hydration method is possible', () => {
        const MODEL__NAME     = 'cats';
        const PROPERTY__NAME  = 'id';
        const MODEL__IDENTITY = Model.identify(MODEL__NAME);
        
        const catConfig = new ModelConfiguration({
                                                     name:       MODEL__NAME,
                                                     properties: {
                                                         id: {
                                                             length: 11,
                                                             type:   'integer'
                                                         }
                                                     }
                                                 });
        
        const cat = new Model;
        
        catConfig.configure(cat).catch(err => console.log(err));
        
        const config         = new PropertyAsReferenceConfiguration({
                                                                        identity:        MODEL__IDENTITY,
                                                                        hydrationMethod: {
                                                                            property: PROPERTY__NAME
                                                                        }
                                                                    });
        config.smEntityProto = Model;
        
        return config.configure(new PropertyAsReferenceDescriptor)
                     .then(propertyAsReferenceDescriptor => {
                         const json__string = JSON.stringify(propertyAsReferenceDescriptor, ' ', 3);
            
                         const propertyName     = cat.identifyProperty(PROPERTY__NAME);
                         const propertyIdentity = Property.identify(propertyName);
                         console.log(json__string);
                         const objFromJSON = JSON.parse(json__string);
                         expect(objFromJSON.hydrationMethod).to.be.an("object");
                         expect(objFromJSON.hydrationMethod).to.equal(propertyIdentity.toJSON());
                     })
    });
});