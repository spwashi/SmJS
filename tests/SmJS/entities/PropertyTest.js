import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('Property', () => {
    const Property     = _src.entities.Property;
    const DataSource   = _src.entities.DataSource;
    const testProperty = new Property('testProperty');
    it('exists', () => {
        expect(testProperty.Symbol).to.be.a('symbol');
        expect(testProperty.Symbol.toString()).to.equal(Symbol(`[${Property.name}]testProperty`).toString())
    });
    it('Can configure DataSource', done => {
        const pn  = 'P_ccd_pn';
        const dsn = 'P_ccd_dsn';
        new DataSource(dsn, {type: 'database'});
        new Property(pn, {source: dsn});
        Property.resolve(pn)
                .then(i => {
                    /** @type {Event|DataSource}  */
                    const [e, property] = i;
                    const dataSource    = property.dataSource;
                    const msg           = dataSource instanceof DataSource ? null : "Could not resolve dataSource properly";
                    done(msg);
                });
    });
});