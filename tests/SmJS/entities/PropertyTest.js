import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('Property', () => {
    const Property     = _src.entities.Property;
    const testProperty = new Property('testProperty');
    it('exists', () => {
        expect(testProperty.Symbol).to.be.a('symbol');
        expect(testProperty.Symbol.toString()).to.equal(Symbol(`[${Property.name}]testProperty`).toString())
    });
});