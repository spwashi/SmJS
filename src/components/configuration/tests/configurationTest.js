import {describe, it} from "mocha";
import {expect} from "chai";
import {Configuration} from "../configuration"
import {errors} from '../constants/index'

class C extends Configuration {
    handlers = {
        title: (config_value, owner, configuration: Configuration) => {
            owner.title = config_value;
            console.log(configuration);
        }
    }
}

describe('Configuration', () => {
    it('Can only be used to configure objects', () => {
        const configuration  = new Configuration;
        const configuredItem = 'string';
        expect(() => configuration.configure(configuredItem)).to.throw(errors.CONFIGURATION__EXPECTED_OBJECT);
    });
    
    it('Can use custom functions ', done => {
        const TITLE         = 'boonman';
        const configuration = new C({title: TITLE});
        const breadman      = {};
        configuration.configure(breadman)
                     .then(owner => {
                         expect(owner).to.deep.equal(breadman);
                         expect(owner.title).to.equal(TITLE);
                         done();
                     })
    });
});