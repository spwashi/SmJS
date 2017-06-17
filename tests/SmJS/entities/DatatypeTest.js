import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const Sm     = require(SMJS_PATH);

describe('Datatype', () => {
    const Datatype     = Sm.entities.Datatype;
    const GenericError = Sm.errors.GenericError;
    const testDatatype = new Datatype('testDatatype');
    it('exists', () => {
        expect(testDatatype.Symbol).to.be.a('symbol');
        expect(testDatatype.Symbol.toString()).to.equal(Symbol(`[${Datatype.name}]testDatatype`).toString())
    });
    it('Can inherit from other Datatypes', () => {
        const p_dt_n     = 'cifd_p_dt_n';
        const child_dt_n = 'cifd_child_dt_n';
        const parent     = Datatype.init(p_dt_n).initializingObject;
        return Datatype.init(child_dt_n, {inherits: p_dt_n})
                       .then(/** @param {Datatype}  */datatype => {
                           expect(datatype).to.be.instanceof(Datatype);
                           expect([...datatype.parents]).to.contain(parent.Symbol);
                       });
    });
    
    it('Can only inherit from one Datatype', () => {
        const p_dt_n     = 'cifd_p_dt_n1';
        const p_dt_n2    = 'cifd_p_dt_n2';
        const child_dt_n = 'cifd_child_dt_n';
        Datatype.init(p_dt_n);
        Datatype.init(p_dt_n2);
        return Datatype.init(child_dt_n, {inherits: [p_dt_n, p_dt_n2]})
                       .then(i => {throw Error('Successfully added more than one datatype- oops');})
                       .catch(e => {
                           expect(e).to.be.instanceof(GenericError);
                           expect(e.message).to.contain('Can only inherit from one Datatype');
                       });
    });
});