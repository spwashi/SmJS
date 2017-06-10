import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('Source', () => {
    const Source     = _src.entities.Source;
    const testSource = new Source('testSource');
    it('exists', () => {
        expect(testSource.Symbol).to.be.a('symbol');
        expect(testSource.Symbol.toString()).to.equal(Symbol(`[${Source.name}]testSource`).toString())
    });
});