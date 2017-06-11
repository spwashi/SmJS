import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('DataSource', () => {
    const DataSource = _src.entities.DataSource;
    const testSource = new DataSource('testSource');
    it('exists', () => {
        expect(testSource.Symbol).to.be.a('symbol');
        expect(testSource.Symbol.toString()).to.equal(Symbol(`[${DataSource.name}]testSource`).toString())
    });
});