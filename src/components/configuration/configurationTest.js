import {describe, it} from "mocha";
import {expect} from "chai";
import {Configuration} from "./configuration"
import {errors} from './constants'

describe('Configuration', () => {
    it('Can only be configured by objects or null ', () => {
        const configuration = new Configuration();
        const config        = 'string';
        expect(() => configuration.configure(config)).to.throw(errors.CONFIGURATION__EXPECTED_OBJECT);
    });
    
    it('Can use custom functions ')
});