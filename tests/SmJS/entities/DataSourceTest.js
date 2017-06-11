import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('DataSource', () => {
    const DataSource = _src.entities.DataSource;
    const testSource = new DataSource('testSource');
    
    DataSource.acceptedTypes = {
        Database: {},
        JSON:     {}
    };
    
    it('exists', () => {
        expect(testSource.Symbol).to.be.a('symbol');
        expect(testSource.Symbol.toString()).to.equal(Symbol(`[${DataSource.name}]testSource`).toString())
    });
    it('Has a type', done => {
        const ds_name = 'hat_ds';
        const ds_type = 'Database';
        new DataSource(ds_name, {
            type: ds_type
        });
        DataSource.resolve(ds_name).then(i => {
            /** @type {DataSource|Event}  */
            let e, dataSource;
            [e, dataSource] = i;
            expect(dataSource.type).to.equal(ds_type);
            done();
        });
    });
    
    it('Has a type that is one of a few pre-configured values', () => {
        let ds_name    = 'hatpcv_ds', ds_type = 'Json';
        const createDS = i => new DataSource(ds_name, {type: 'Moot'});
        // expect(createDS).to.throw(new Error);
    });
});