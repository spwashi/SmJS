/**
 * Created by Sam Washington on 5/20/17.
 */
const Std = require('../std/').Std;
const _   = require('lodash');
import {mapToObj} from "../util/index";
/**
 * @class ConfiguredEntity
 */
export default class ConfiguredEntity extends Std {
    static get smID() {return 'ConfiguredEntity'; }
    
    constructor(name, config = {}) {
        name = name || config.name;
        super(name);
        this._parentSymbols = new Set;
        config.configName   = config.configName || name;
        this._storeOriginalConfiguration(config);
    }
    
    initialize(config) {
        let inherits                     = config.inherits;
        const completeInitialInheritance = this._completeInitialInheritance(inherits);
        return super.initialize(config)
                    .then(i => completeInitialInheritance)
                    .then(i => this.configure(config))
                    .then(i => this)
    }
    
    /** @return {Set} */
    get parents() { return this._parentSymbols; }
    
    /**
     * This is the name as it was used when we were initially configuring whatever this was.
     */
    get configName() {
        return this.getOriginalConfiguration().configName;
    }
    
    get inheritables() {
        return this.getInheritables();
    }
    
    /**
     * Get an array of the Fields we're going to encode in JSON
     * @return {Set<string>}
     */
    get jsonFields() {
        return new Set(['smID']);
    }
    
    toJSON() {
        const jsonFields = this.jsonFields;
        const json_obj   = {};
        jsonFields.forEach(fieldName => {
            const fn_name = `toJSON_${fieldName}`;
            let item      = this[fn_name] ? this[fn_name]() : this[fieldName];
            if (item instanceof Map) item = mapToObj(item);
            json_obj[fieldName] = item;
        });
        return json_obj;
    }
    
    /**
     * Set the properties of this object using another object.
     *
     * @param properties
     * @return {Promise.<*>}
     */
    configure(properties) {
        // Array of all the Promises we want to resolve before we count this as configured
        let promises    = [];
        const CONFIGURE = this.EVENTS.item('configure');
        
        this.send(CONFIGURE.BEGIN);
        // Iterate through the properties and wait for them to resolve
        for (let property_name in properties) {
            if (!properties.hasOwnProperty(property_name)) continue;
            
            // Look for a configure_ function. If it exists, add it to the promises taht we are going to wait for resolution-wise
            const fn_name     = 'configure_' + property_name;
            /** @type {undefined|function}  */
            const fn_         = this[fn_name];
            // Push the function's resolution if there is a function, and the name of the function otherwise
            const loopPromise =
                      typeof fn_ === 'function'
                          ? fn_.apply(this, [properties[property_name]])
                          : Promise.resolve(fn_name);
            promises.push(loopPromise);
        }
        return Promise.all(promises).then(i => this.send(CONFIGURE.COMPLETE));
    }
    
    /**
     * Get an object of everything that this object is willing to allow us to
     * @return {{}}
     */
    getInheritables() {
        return this.getOriginalConfiguration();
    }
    
    /**
     *
     * @param item
     * @return {*|Promise<[]>}
     */
    inherit(item) {
        if (!item) return Promise.resolve([]);
        
        const ITEM_INHERITANCE = Std.EVENTS.item('inheritance').item('item');
        return this.constructor
                   .resolve(item)
                   .then(
                       (result) => {
                           /** @type {Event} event */
                           let [event, parent] = result;
                           this.send(ITEM_INHERITANCE.BEGIN.Symbol, parent);
                           /** @type {ConfiguredEntity} parent */
                           if (!(parent instanceof this.constructor)) {
                               // We can only inherit from things that are part of this family.
                               throw new Error('Cannot accept ' + (String(parent)));
                           }
                
                           // Say that we've inherited from this item
                           this._parentSymbols.add(parent.Symbol);
                
                           // Only inherit what the parent is willing to give
                           const newConfiguration = Object.assign({}, parent.inheritables, this.getOriginalConfiguration());
                           const configure        = this.configure(newConfiguration);
                
                           return configure.then(i => {
                               return this.send(ITEM_INHERITANCE.COMPLETE, item);
                           });
                       });
    }
    
    /**
     * Get the original obect (or a clone of it) that was used to configure this object
     * @return {{}}
     */
    getOriginalConfiguration() {
        return this._originalConfig;
    }
    
    //region PrivateMethods
    /**
     * Add the Original Configuration as an attribute of this object.
     * @param original_config
     * @return {ConfiguredEntity}
     * @private
     */
    _storeOriginalConfiguration(original_config) {
        this._originalConfig = _.cloneDeep(original_config);
        return this;
    }
    
    /**
     * Inherit from all of the parent identifiers we said we want to inherit from in the original configuration
     * @param parent_identifiers
     * @return {Promise<Std>}
     * @private
     */
    _completeInitialInheritance(parent_identifiers) {
        parent_identifiers     = Array.isArray(parent_identifiers) ? parent_identifiers : [parent_identifiers];
        const INHERIT          = Std.EVENTS.item('inheritance').item('configuration');
        const inheritedFollows = [];
        parent_identifiers.forEach(item => {
            const pId = this.inherit(item);
            inheritedFollows.push(pId);
        });
        return this.send(this.EVENTS.item(INHERIT.BEGIN).STATIC, this)
                   .then(i => Promise.all(inheritedFollows))
                   .then(i => this.send(this.EVENTS.item(INHERIT.COMPLETE).STATIC, this))
                   .catch(i => {throw i});
    }
    //endregion
}