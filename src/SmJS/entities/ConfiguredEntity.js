/**
 * Created by Sam Washington on 5/20/17.
 */
const Std      = require('../std/').Std;
const _reject_ = i => {throw i};

const _ = require('lodash');
/**
 * @class ConfiguredEntity
 */
export default class ConfiguredEntity extends Std {
    static get name() {return 'ConfiguredEntity'; }
    
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
     * Complete the initialization (shortcut for complete)
     * @return {Promise}
     * @private
     */
    _finishInit() {
        return this._completeInit(ConfiguredEntity.name);
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
                   .then(i => Promise.all(inheritedFollows), _reject_)
                   .then(i => this.send(this.EVENTS.item(INHERIT.COMPLETE).STATIC, this), _reject_)
                   .catch(i => {throw i});
    }
    
    constructor(name, config = {}) {
        if (!config && typeof name === 'object') config = name;
        name              = config.name || name;
        config.configName = config.configName || name;
        super(name);
        this._parentSymbols = new Set;
        this._storeOriginalConfiguration(config);
        let inherits = config.inherits;
        /**
         * @protected
         */
        this._parentPromise = this._parentPromise
                                  .then(i => this._completeInitialInheritance(inherits), _reject_)
                                  .then(i => this.configure(config), _reject_)
                                  .then(i => this._finishInit(), _reject_)
                                  .catch(e => {
                                      this.send(this.EVENTS.item(Std.EVENTS.item('init').ERROR), e);
                                      throw e;
                                  });
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
        let promises = [];
        
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
        return Promise.all(promises);
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
    
        const ITEM_INHERITANCE = Std.EVENTS.item('inheritance').item('item');
        return this.constructor
                   .resolve(item)
                   .then(
                       (result) => {
    
                           /** @type {Event} event */
                           let [event, parent] = result;
                           console.log(this.symbolName, parent.symbolName);
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
                           }).catch(i => {
                               console.error(i);
                               throw  i;
                           })
                       });
    }
}