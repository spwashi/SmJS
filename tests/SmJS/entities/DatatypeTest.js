import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const Sm     = require(SMJS_PATH);

describe('Datatype', () => {
    const Datatype     = Sm.entities.Datatype;
    const testDatatype = new Datatype('testDatatype');
    it('exists', () => {
        expect(testDatatype.Symbol).to.be.a('symbol');
        expect(testDatatype.Symbol.toString()).to.equal(Symbol(`[${Datatype.name}]testDatatype`).toString())
    });
    it('Can inherit from other Datatypes', done => {
        const p_dt_n     = 'cifd_p_dt_n';
        const child_dt_n = 'cifd_child_dt_n';
        const parent     = new Datatype(p_dt_n);
        new Datatype(child_dt_n);
        Datatype.resolve(child_dt_n).then(i => {
            /** @type {Datatype}  */
            const datatype = i[1] || null;
            expect(datatype).to.be.instanceof(Datatype);
            expect([...datatype.parents]).to.contain(parent.Symbol);
        });
    });
});