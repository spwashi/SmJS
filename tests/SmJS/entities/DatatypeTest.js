import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('Datatype', () => {
    const Datatype     = _src.entities.Datatype;
    const DataSource   = _src.entities.DataSource;
    const testDatatype = new Datatype('testDatatype');
    it('exists', () => {
        expect(testDatatype.Symbol).to.be.a('symbol');
        expect(testDatatype.Symbol.toString()).to.equal(Symbol(`[${Datatype.name}]testDatatype`).toString())
    });
});