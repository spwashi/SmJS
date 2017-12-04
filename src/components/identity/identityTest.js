import {describe, it} from "mocha";
import {expect} from "chai";
import {Identity} from "./identity"
import {errors} from './constants'

describe('Identity', () => {
    it('Can only be initialized statically', () => {
        expect(Identity.init('name')).to.be.instanceOf(Identity);
        expect(() => new Identity).to.throw(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
    });
    
    it('Can orixy identity creation', () => {
        expect(Identity.init('name').item('item_1')).to.be.instanceOf(Identity);
        expect(() => new Identity).to.throw(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
    })
});