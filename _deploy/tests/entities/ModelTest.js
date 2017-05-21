import {describe, it} from "mocha";
import {models} from "../../../_deploy/src/_config";
const expect  = require('chai').expect;
const _deploy = require('../../../_deploy');

describe('Model', () => {
    const Std             = _deploy.std.Std;
    const Model           = _deploy.entities.Model;
    const getDefaultModel = i => { return new Model('_', models._); };
    
    it('exists', () => {
        const testModel = new Model('test', {});
        expect(testModel.Symbol).to.be.a('symbol');
        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}].test`).toString());
    });
    it('Can be initialized w Properties', done => {
        const testModel = getDefaultModel();
        expect(testModel.Symbol).to.be.a('symbol');
        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}]._`).toString());
        Model.resolve('_').then(i => {
            let [event, model] = i;
            done();
        });
    });
    
    it('Can inherit another model', done => {
        const parentModel      = new Model('parentModel');
        const childModel       = new Model('childModel', {follows: parentModel.name});
        const INHERIT_COMPLETE = Std.EVENTS.item('inherit').COMPLETE;
        
        childModel.receive(childModel.EVENTS.item(INHERIT_COMPLETE)).then(result => {
            let [event, childModel] = result;
            
            if (!(childModel instanceof Model)) return done(`return is of wrong type (${typeof childModel})`);
            
            if (childModel.parents.has(parentModel.Symbol)) return done();
            
            return done('Could not inherit parent');
        });
    })
});