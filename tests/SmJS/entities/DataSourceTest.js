import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const Sm     = require(SMJS_PATH);
const expect = require('chai').expect;
require('chai-as-promised');
const _src = require(SMJS_PATH);

describe('DataSource', () => {
    const DataSource = _src.entities.DataSource;
    const testSource = DataSource.create('testSource');
    
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
        DataSource.create(ds_name, {
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
    
    it('Has a type that is one of a few pre-configured values', done => {
        let ds_name = 'hatpcv_ds', ds_type = 'Json';
        DataSource.create(ds_name, {type: 'Moot'});
        DataSource.receive(Sm.std.Std.EVENTS.item('init').ERROR).then(i => {
            const [event, error] = i;
            const message        = error instanceof TypeError ? null : 'Successfully set bad value';
            done(message);
        })
        
    });
});