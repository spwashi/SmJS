import {describe, it} from "mocha";
import Configuration from "./configuration"
import {PropertyAsProxyDescriptor} from "./propertyAsProxy";

describe('Property As Proxy Test', () => {
    it('Exists as a class with a configuration', () => {
        const config = new Configuration;
        
        config.configure(new PropertyAsProxyDescriptor)
              .then(propertyAsProxyDescriptor => {
                  console.log(JSON.stringify(propertyAsProxyDescriptor, ' ', 3));
              })
    });
});