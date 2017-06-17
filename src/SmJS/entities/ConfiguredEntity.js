/**
 * Created by Sam Washington on 5/20/17.
 */
const Std = require('../std/').Std;
const _   = require('lodash');
/**
 * @class ConfiguredEntity
 */
export default class ConfiguredEntity extends Std {
    static get name() {return 'ConfiguredEntity'; }
    
    constructor(name, config = {}) {
        name = config.name || name;
        super(name);
        this._parentSymbols = new Set;
        this._storeOriginalConfiguration(config);
    }
    
    initialize(config) {
        let inherits = config.inherits;
        const name   = this.name;
        if (!config && typeof name === 'object') config = name;
        config.configName = config.configName || name;
        return super.initialize(config)
                    .then(i => this._completeInitialInheritance(inherits))
                    .then(i => this.configure(config))
                    .then(i => this)
    }
    
    get name() {return this._name;}
    
    /** @return {Set} */
    get parents() { return this._parentSymbols; }
    
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
     * Get the original obect (or a clone of it) that was used to configure this object
     * @return {{}}
     */
    getOriginalConfiguration() {
        return this._originalConfig;
    }
    
    /**
     * Inherit from all of the parent identifiers we said we want to inherit from in the original configuration
     * @param parent_identifiers
     * @return {Promise<Std>}
     * @private
     */
    _completeInitialInheritance(parent_identifiers) {
        parent_identifiers     = Array.isArray(parent_identifiers) ? parent_identifiers : [parent_identifiers];
        const INHERIT          = Std.EVENTS.item('inherit');
        const inheritedFollows = parent_identifiers.map(item => this.inherit(item));
        return this.send(this.EVENTS.item(INHERIT.BEGIN).STATIC, this)
                   .then(i => Promise.all(inheritedFollows))
                   .then(i => this.send(this.EVENTS.item(INHERIT.COMPLETE).STATIC, this))
    }
    
    /**
     * This is the name as it was used when we were initially configuring whatever this was.
     */
    get configName() {
        return this.getOriginalConfiguration().configName;
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
    
    get inheritables() {
        return this.getInheritables();
    }
    
    /**
     *
     * @param item
     * @return {*|Promise<[]>}
     */
    inherit(item) {
        if (!item) return Promise.resolve([]);
        
        return this.constructor
                   .resolve(item)
                   .then(
                       (result) => {
                           /** @type {Event} event */
                           let [event, parent] = result;
                
                           /** @type {ConfiguredEntity} parent */
                           if (!(parent instanceof this.constructor)) {
                               // We can only inherit from things that are part of this family.
                               throw new Error('Cannot accept ' + (String(parent)));
                           }
                
                           // Say that we've inherited from this item
                           this._parentSymbols.add(parent.Symbol);
                
                           // Only inherit what the parent is willing to give
                           return this.configure(Object.assign({}, parent.inheritables, this.getOriginalConfiguration()));
                       }, null);
    }
}