import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
const _src   = require(SMJS_PATH);

describe('Property', () => {
    const Property     = _src.entities.Property;
    const DataSource   = _src.entities.DataSource;
    const testProperty = Property.init('testProperty').initializingObject;
    it('exists', () => {
        expect(testProperty.Symbol).to.be.a('symbol');
        expect(testProperty.Symbol.toString()).to.equal(Symbol(`[${Property.name}]testProperty`).toString())
    });
    it('Can configure DataSource', () => {
        const pn  = 'P_ccd_pn';
        const dsn = 'P_ccd_dsn';
        DataSource.init(dsn, {type: 'database'});
        return Property.init(pn, {source: dsn})
                       .then(/**@param Property*/property => {
                           const dataSource = property.dataSource;
                           if (!(dataSource instanceof DataSource)) throw new Error("Could not resolve dataSource properly");
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