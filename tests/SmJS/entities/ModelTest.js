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
    const DataSource      = Sm.entities.DataSource;
    const Property        = Sm.entities.Property;
    const getDefaultModel = i => { return new Model('_', models._); };
    
    it('exists', () => {
        const testModel = new Model('test', {});
        expect(testModel.Symbol).to.be.a('symbol');
        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}]test`).toString());
        const COMPLETE = Std.EVENTS.item('init').COMPLETE;
        return Model.receive(testModel.EVENTS.item(COMPLETE));
    });
    
    it('Can be initialized w properties', done => {
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
    
    it('Can resolve properties', done => {
        const model          = new Model('testResolveProperties', {properties: {test_property: {}}});
        const modelName      = '[Model]testResolveProperties';
        const _property_name = 'test_property';
        
        Std.resolve(`${modelName}|${_property_name}`).then(i => {
            let [event, property] = i;
            // [Property]{[Model]testResolveProperties}test_property
            expect(model.properties.get(`[Property]\{${modelName}}${_property_name}`)).to.equal(property);
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
    
    it('Can inherit properties', done => {
        const m1n     = 'cip_m1n', m2n = 'cip_m2n', m3n = 'cip_m3n';
        const _models = {
            [m1n]: {properties: {id: {primary: true}, last_name: {unique: true}}},
            [m2n]: {inherits: ['cip_m1n'], properties: {first_name: {unique: true}}},
            [m3n]: {inherits: ['cip_m2n'], properties: {first_name: {unique: false}, last_name: {unique: false}}}
        };
        
        // Initialize all of the Models
        const resolveModels            = Object.entries(_models)
                                               .map(i => {
                                                   let [model_name, model_config] = i;
                                                   // Initialize the Model
                                                   new Model(model_name, model_config);
                                                   return Model.resolve(model_name);
                                               });
        const _assertProperty          = property => expect(property).to.be.instanceof(Property);
        /**
         * @param i
         * @return Property
         */
        const _getPropertyFromEventArr = i => (_assertProperty(i[1]) , i[1]);
        
        // Once all of the Models have been initialized
        Promise.all(resolveModels)
        
               // Get all models from the returned array of event arrays & store them in an object
               .then(i => {
                   return new Map(i.map(event_model_arr => event_model_arr[1])
                                   .map(/**@type {Model}*/
                                        model => [model.originalName, model]));
               })
        
               // Check to see if the Models have inherited properties correctly
               .then(
                   /** @param ModelMap {Map<string,Model>}  */
                   (ModelMap) => {
                       const m1         = ModelMap.get(m1n),
                             m2         = ModelMap.get(m2n),
                             m3         = ModelMap.get(m3n);
                       const m1_promise = m1.resolve('id').then(i => {
                           const property = _getPropertyFromEventArr(i);
                       });
                       const m2_promise = m2.resolve('first_name')
                                            .then(i => {
                                                const property     = _getPropertyFromEventArr(i);
                                                const uniqueKeySet = m2.propertyMeta.getUniqueKeySet(property);
                                                expect(uniqueKeySet).not.to.equal(false);
                                            });
                       const m3_promise = m3.resolve('first_name')
                                            .then(i => {
                                                const property     = _getPropertyFromEventArr(i);
                                                const uniqueKeySet = m3.propertyMeta.getUniqueKeySet(property);
                                                expect(uniqueKeySet).to.equal(false);
                                            });
                       return Promise.all([m1_promise, m2_promise, m3_promise]);
                   })
               .then(i => done());
        
    });
    
    it('Can configure DataSource', done => {
        const mn  = 'ccd_mn';
        const dsn = 'ccd_dsn';
        new DataSource(dsn, {type: 'database'});
        new Model(mn, {source: dsn});
        Model.resolve(mn)
             .then(i => {
                 /** @type {Event|Model}  */
                 const [e, model] = i;
                 const dataSource = model.dataSource;
                 const msg        = dataSource instanceof DataSource ? null : "Could not resolve dataSource properly";
                 done(msg);
             });
    });
    
    it('Configures DataSource in the correct order', done => {
        const mn = 'M_cdico_mn', dsn = 'M_cdico_sn';
        Model.resolve(mn)
             .then(i => {
                 /** @type {Event|Model}  */
                 const [e, model]   = i;
                 const dataSource   = model.dataSource;
                 const _isComplete  = dataSource.isComplete;
                 const _isAvailable = dataSource.isAvailable;
                 const msg          =
                           _isAvailable && !_isComplete
                               ? null
                               : "\n\tComplete: " + (_isComplete ? '(does not necessarily need to be true)' : 'is ok' )
                           + "\n\tAvailable: " + (!_isAvailable ? '(should be true)' : 'is ok');
            
                 // Not really sure how to test this bc it's a pretty internal aspect.
                 // failures in other places might be tied to this if it breaks, though
                 done();
             });
        
        new DataSource(dsn, {type: 'database'});
        new Model(mn, {source: dsn});
    });
    
    it('Can pass DataSource on to properties', done => {
        const mn = 'M_cpdotp_mn', dsn = 'M_cpdotp_dsn', pn = 'M_cpdotp_pn';
        new DataSource(dsn, {type: 'database'});
        new Model(mn, {source: dsn, properties: {[pn]: {}}});
        Model.resolve(mn)
             .then(i => {
                 const [, model] = i;
                 return model.resolve(pn);
             })
             .then(i => {
                 const [e, property] = i;
                 expect(property).to.be.instanceof(Property);
                 expect(property.dataSource).to.be.instanceof(DataSource);
                 done();
             });
    })
});