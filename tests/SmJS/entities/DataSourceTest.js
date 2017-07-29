import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";

const Sm     = require(SMJS_PATH);
const expect = require('chai').expect;
require('chai-as-promised');
const _src = require(SMJS_PATH);

describe('DataSource', () => {
    const DataSource = _src.entities.DataSource;
    const TypeError  = _src.errors.TypeError;
    it('exists', () => {
        return DataSource.init('testSource')
                         .then(testSource => {
                             expect(testSource.Symbol).to.be.a('symbol');
                             expect(testSource.Symbol.toString()).to.equal(Symbol(`[${DataSource.name}]testSource`).toString())
                         });
    });
    it('Has a type', done => {
        const ds_name = 'hat_ds';
        const ds_type = 'database';
        DataSource.init(ds_name);
        DataSource.resolve(ds_name).then(i => {done();});
    });
    it('Can be JSON', () => {
        return DataSource.init('DS_cbj_cen')
                         .then(model => {
                             const stringify = JSON.stringify(model);
                             const parse     = JSON.parse(stringify);
                             expect(parse).to.haveOwnProperty('smID');
                         });
    })
});