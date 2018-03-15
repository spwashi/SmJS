import {expect} from "chai"
import {describe, it} from "mocha";
import PropertyAsProxyConfiguration from "./configuration"
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";
import {Model} from "../../model/model";
import {ModelConfiguration} from "../../model/configuration";

describe('Property As Proxy Test', () => {
    it('Exists as a class with a configuration', done => {
        const boonman__identity = Model.identify('boonman');
        const boonmanConfig     = new ModelConfiguration({name: 'boonman'});
        
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
        
        footballConfig.configure(new Model)
                      .catch(err => console.log(err));
        
        const config              = new PropertyAsProxyConfiguration({
                                                                         roleName: "sport_item",
                                                                         identity: football__identity
                                                                     });
        // This is how we know what kind of identity we are Proxying
        config.propertyOwnerProto = Model;
        
        const proxyDescriptorConfigured = config.configure(new PropertyAsProxyDescriptor);
        
        proxyDescriptorConfigured
            .then(
                propertyAsProxyDescriptor => {
                    const json__string = JSON.stringify(propertyAsProxyDescriptor, ' ', 3);
                    console.log(json__string);
                    const objFromJSON = JSON.parse(json__string);
                    expect(objFromJSON.identity).to.equal(football__identity.toJSON());
                })
            .then(() => done());
        
    });
});