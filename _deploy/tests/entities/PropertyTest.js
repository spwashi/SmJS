import {describe, it} from "mocha";
const expect  = require('chai').expect;
const _deploy = require('../../../_deploy');

describe('Property', () => {
    const Property     = _deploy.entities.Property;
    const testProperty = new Property('testProperty');
    it('exists', () => {
        expect(testProperty.Symbol).to.be.a('symbol');
        expect(testProperty.Symbol.toString()).to.equal(Symbol(`[${Property.name}]testProperty`).toString())
    });
});