import {expect} from "chai"
import {describe, it} from "mocha";
import PropertyAsProxyConfiguration from "./configuration"
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";
import {Model} from "../../model/model";
import {ModelConfiguration} from "../../model/configuration";
import {Property} from "../property";

describe('Property As Proxy Test', () => {
    it('Exists as a class with a configuration', done => {
        const boonmanConfig = new ModelConfiguration({name: 'boonman'});
        
        boonmanConfig.configure(new Model)
                     .catch(err => console.log(err));
        
        const config = new PropertyAsProxyConfiguration({roleName: "person"});
        
        config.configure(new PropertyAsProxyDescriptor)
              .then(propertyAsProxyDescriptor => {
                  const json__string = JSON.stringify(propertyAsProxyDescriptor, ' ', 3);
                  console.log(json__string);
                  const objFromJSON = JSON.parse(json__string);
                  expect(objFromJSON.roleName).to.equal("person");
                  done();
              })
    });
    it('waits for the configuration of the Proxied identity to be complete before using it', done => {
        const MODEL__NAME        = 'footballs';
        const football__identity = Model.identify(MODEL__NAME);
        const footballConfig     = new ModelConfiguration({name: MODEL__NAME});
        
        footballConfig.configure(new Model).catch(err => console.log(err));
        
        const config         = new PropertyAsProxyConfiguration({
                                                                    roleName: "sport_item",
                                                                    identity: football__identity
                                                                });
        // This is how we know what kind of identity we are Proxying
        config.smEntityProto = Model;
        
        const proxyDescriptorConfigured = config.configure(new PropertyAsProxyDescriptor);
        
        proxyDescriptorConfigured.then(
            propertyAsProxyDescriptor => {
                const json__string = JSON.stringify(propertyAsProxyDescriptor, ' ', 3);
                console.log(json__string);
                const objFromJSON = JSON.parse(json__string);
                expect(objFromJSON.identity).to.equal(football__identity.toJSON());
            })
                                 .then(() => done());
    });
    it('Can verify that the configured hydration method is possible', done => {
        const MODEL__NAME    = 'cats';
        const PROPERTY__NAME = 'id';
        
        const cat__identity = Model.identify(MODEL__NAME);
        const catConfig     = new ModelConfiguration({
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
        const config         = new PropertyAsProxyConfiguration({
                                                                    roleName:  "sport_item",
                                                                    identity:  cat__identity,
                                                                    hydration: {
                                                                        property: PROPERTY__NAME
                                                                    }
                                                                });
        // This is how we know what kind of identity we are Proxying
        config.smEntityProto = Model;
        
        const proxyDescriptorConfigured = config.configure(new PropertyAsProxyDescriptor);
        
        proxyDescriptorConfigured.then(propertyAsProxyDescriptor => {
                                     const json__string = JSON.stringify(propertyAsProxyDescriptor, ' ', 3);
            
                                     const propertyName     = cat.createPropertyIdentity(PROPERTY__NAME);
                                     const propertyIdentity = Property.identify(propertyName);
                                     console.log(json__string);
                                     const objFromJSON = JSON.parse(json__string);
                                     expect(objFromJSON.hydration).to.be.an("object");
                                     expect(objFromJSON.hydration.property).to.equal(propertyIdentity.toJSON());
                                 })
                                 .then(() => done());
    });
});