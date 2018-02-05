import {describe, it} from "mocha";
import {expect} from "chai";
import {Identity} from "../";
import {errors} from '../constants/index'
import {createIdentityManager} from "../components/identity";

describe('Identity', () => {
    it('Can only be initialized statically', () => {
        const identityManager = createIdentityManager('name');
        expect(identityManager.instance('bread')).to.be.instanceOf(Identity);
        
        expect(() => new Identity).to.throw(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
    });
    
    it('Can do identity creation', () => {
        const item_1 = createIdentityManager('thing').instance('item_1');
        expect(item_1).to.be.instanceOf(Identity);
    });
});