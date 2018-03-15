import {expect} from "chai"
import {describe, it} from "mocha";
import Configuration from "./configuration"
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";
import {Model} from "../../model/model";
import {ModelConfiguration} from "../../model/configuration";

describe('Property As Proxy Test', () => {
    it('Exists as a class with a configuration', done => {
        const boonman__identity = Model.identify('boonman');
        const boonmanConfig     = new ModelConfiguration({name: 'boonman'});
        
        boonmanConfig.configure(new Model)
                     .catch(err => console.log(err));
        
        const config = new Configuration({roleName: "person", identity: boonman__identity});
        
        config.configure(new PropertyAsProxyDescriptor)
              .then(propertyAsProxyDescriptor => {
                  const json__string = JSON.stringify(propertyAsProxyDescriptor, ' ', 3);
                  console.log(json__string);
                  const objFromJSON = JSON.parse(json__string);
                  expect(objFromJSON.roleName).to.equal("person");
                  done();
              })
    });
});