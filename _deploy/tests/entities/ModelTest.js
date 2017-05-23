import {describe, it} from "mocha";
import {models} from "../../../_deploy/src/_config";
const expect  = require('chai').expect;
const _deploy = require('../../../_deploy');

describe('Model', () => {
    const Std             = _deploy.std.Std;
    const SymbolStore     = _deploy.std.symbols.SymbolStore;
    const Model           = _deploy.entities.Model;
    const Property        = _deploy.entities.Property;
    const getDefaultModel = i => { return new Model('_', models._); };
    
    it('exists', () => {
        const testModel = new Model('test', {});
        expect(testModel.Symbol).to.be.a('symbol');
        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}]test`).toString());
    });
    
    it('Can be initialized w Properties', done => {
        const testModel = getDefaultModel();
        expect(testModel.Symbol).to.be.a('symbol');
        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}]_`).toString());
        Model.resolve('_').then(i => {
            let [event, model] = i;
            done();
        });
    });
    
    const INHERIT_COMPLETE = Std.EVENTS.item('inherit').COMPLETE;
    it('Can inherit another model', done => {
        const parentModel = new Model('parentModel');
        const childModel  = new Model('childModel', {follows: parentModel.name});
    
        childModel.receive(childModel.EVENTS.item(INHERIT_COMPLETE))
                  .then(result => {
                      let [event, childModel] = result;
    
                      if (!(childModel instanceof Model)) return done(`Return is of wrong type (${typeof childModel})`);
    
                      if (childModel.parents.has(parentModel.Symbol)) return done();
    
                      return done('Could not inherit parent');
                  });
    });
    
    it('Can inherit multiple models', done => {
        const parentModel1 = new Model('parentModel1');
        const parentModel2 = new Model('parentModel2');
        const childModel   = new Model('childModel', {follows: ['parentModel2', 'parentModel1']});
        
        childModel.receive(childModel.EVENTS.item(INHERIT_COMPLETE)).then(i => {
            let [event, childModel] = i;
            if (childModel.parents.has(parentModel1.Symbol) && childModel.parents.has(parentModel2.Symbol)) return done();
            done('Incomplete')
        })
    });
    
    it('Can resolve Properties', done => {
        const model          = new Model('testResolveProperties', {properties: {test_property: {}}});
        const modelName      = '[Model]testResolveProperties';
        const _property_name = 'test_property';
        
        Std.resolve(`${modelName}|${_property_name}`).then(i => {
            let [event, property] = i;
            let error;
            // SmPHP-30
            expect(model.Properties[`[Property]\{${modelName}}${_property_name}`]).to.equal(property);
            expect(property).to.be.instanceof(Property);
            
            return model.resolve(_property_name).then(prop => done());
        });
    })
    
});