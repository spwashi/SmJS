import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
/** @alias {Sm}  */
const Sm     = require(SMJS_PATH);

describe('Property', () => {
    /**@alias Property */
    const Property     = Sm.entities.Property;
    const DataSource   = Sm.entities.DataSource;
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
    it('Can inherit from other Properties', done => {
        const parent_pn = 'cifp_parent_pn', child_pn = 'cifp_child_pn';
        let parent      = new Property(parent_pn, {});
        let child       = new Property(child_pn, {inherits: [parent_pn]});
        
        Property.resolve(child_pn)
                .then(i => {
                    /** @type {Property} */
                    const property = i[1];
                    expect([...property.parents]).to.contain((parent.Symbol));
                    done();
                });
    })
});