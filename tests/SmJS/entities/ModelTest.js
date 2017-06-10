import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const expect = require('chai').expect;
/** @alias {Sm}  */
const Sm     = require(SMJS_PATH);
const models = Sm._config.models;
describe('Model', () => {
    const Std             = Sm.std.Std;
    const SymbolStore     = Sm.std.symbols.SymbolStore;
    const Model           = Sm.entities.Model;
    const Property        = Sm.entities.Property;
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
        Model.resolve('_').then(i => {done();});
    });
    
    const INHERIT_COMPLETE = Std.EVENTS.item('inherit').COMPLETE;
    it('Can inherit another model', done => {
        const parentModel = new Model('parentModel');
        const childModel  = new Model('childModel', {inherits: parentModel.name});
        
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
        const childModel   = new Model('childModel', {inherits: ['parentModel2', 'parentModel1']});
        
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
            // [Property]{[Model]testResolveProperties}test_property
            expect(model.Properties[`[Property]\{${modelName}}${_property_name}`]).to.equal(property);
            expect(property).to.be.instanceof(Property);
            
            return model.resolve(_property_name).then(prop => done());
        });
    });
    
    it('Can register Primary properties', done => {
        const _model_name    = 'primary_test_mn';
        const _property_name = 'primary_test_pn';
        const model_name     = `[Model]${_model_name}`;
        const model          = new Model(_model_name, {properties: {[_property_name]: {primary: true, unique: true}}});
        Std.resolve(`${model_name}|${_property_name}`).then(i => {
            /** @type {Property} property */
            let [event, property] = i;
            const primaryKeySet   = model.propertyMeta.getPrimaryKeySet(property);
            const message         = primaryKeySet ? null : 'Could not successfully incorporate primary key';
            done(message);
        });
    });
    
    it('Can register Unique properties', done => {
        /** @type property2 {Property}  */
        let property2;
        const _model_name     = 'unique_test_mn';
        const _property_name  = 'unique_test_pn';
        const _property_name2 = 'unique_test_pn2';
        const model_name      = `[Model]${_model_name}`;
    
        new Model(_model_name, {
            properties: {
                [_property_name]:  {primary: true, unique: true},
                [_property_name2]: {unique: true},
            }
        });
    
        Std.resolve(`${model_name}|${_property_name2}`)
           .then(i => [, property2] = i)
           .then(i => Std.resolve(model_name))
           .then(i => {
               let [e, model]     = i;
               const uniqueKeySet = model.propertyMeta.getUniqueKeySet(property2);
               const message      =
                       !uniqueKeySet
                           ? 'Could not successfully incorporate unique key'
                           : (uniqueKeySet.get('unique_key').size < 2 ? 'Missing one property' : null);
               done(message);
           });
    });
    
    it('Can inherit Properties', done => {
        const m1n     = 'cip_m1n', m2n = 'cip_m2n', m3n = 'cip_m3n';
        const _models = {
            [m1n]: {properties: {id: {primary: true}, last_name: {unique: true}}},
            [m2n]: {properties: {first_name: {unique: true}}},
            [m3n]: {properties: {first_name: {unique: false}, last_name: {unique: false}}}
        };
        
        // Initialize all of the Models
        const resolveModels = Object.entries(_models)
                                    .map(i => {
                                        let [model_name, model_config] = i;
                                        // Initialize the Model
                                        new Model(model_name, model_config);
                                        return Model.resolve(model_name);
                                    });
        
        // Once all of the Models have been initialized
        Promise.all(resolveModels)
        
               // Get all models from the returned array of event arrays & store them in an object
               .then(i => {
                   return new Map(i.map(event_model_arr => event_model_arr[1])
                                   .map(/**@type {Model}*/
                                        model => [model.originalName, model]));
               })
        
               // Check to see if the Models have inherited Properties correctly
               .then(
                   /** @param ModelMap {Map<string,Model>}  */
                   (ModelMap) => {
                       const m1 = ModelMap.get(m1n), m2 = ModelMap.get(m2n), m3 = ModelMap.get(m3n);
                
                       const m2_promise = m2.resolve('first_name')
                                            .then(i => {
                                                let [, property] = i;
                                                expect(property).to.be.instanceof(Property);
                                                expect(m2.propertyMeta.getUniqueKeySet(property)).not.to.be.false;
                                            });
                       const m3_promise = m3.resolve('id')
                                            .then(i => {
                                                let [, property] = i;
                                                expect(property).to.be.instanceof(Property);
                                            });
                       return Promise.all([m2_promise, m3_promise]);
                   })
               .then(i => done());
        
    })
});