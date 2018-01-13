import {describe, it} from "mocha";
import {expect} from "chai";
import {Identity} from "../";
import {errors} from '../constants/index'
import {createIdentityManager} from "../components/identity";

describe('Identity', () => {
    it('Can only be initialized statically', () => {
        const identityManager = createIdentityManager('name');
        expect(identityManager.item('bread')).to.be.instanceOf(Identity);
        
        expect(() => new Identity).to.throw(errors.IDENTITY__PRIVATE_CONSTRUCTOR);
    });
    
    it('Can Proxy identity creation', () => {
        const item_1 = createIdentityManager('thing').item('item_1');
    
        // playing around with this syntax...
        //  Advantage is --
        //      - this would be easy to find and replace
        //      - it allows us to easily define interfaces that match this structure in typescript
        const bread = item_1.item('B').RELATIONSHIP.ADD.BEGIN;
    
        // this is more interesting to me and feels more sustainable,
        //  because then we could import the ACTIONS and have consistent reference to specific actions in a way that implies their relationships
        const bread_EX = item_1.item(ACTIONS.RELATIONSHIP.ADD.BEGIN);
        
        console.log(bread);
        
        expect(item_1).to.be.instanceOf(Identity);
    });
});