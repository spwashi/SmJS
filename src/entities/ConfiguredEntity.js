/**
 * Created by Sam Washington on 5/20/17.
 */
import merge from "deepmerge";
import {mapToObj} from "../util/index";
import {Std} from "../std/";
import _ from "lodash";

/**
 * @class ConfiguredEntity
 */
export default class ConfiguredEntity extends Std {
    constructor(name, config = {}) {
    
        if (typeof name === "object" && name) {
            config = name;
            name   = null;
        }
        
        name = name || config.name;
        super(name);
        this._parentSymbols = new Set;
        this._parents       = new Set;
        config.configName   = config.configName || name;
        this._storeOriginalConfiguration(config);
    }
    
    static get smID() {return 'ConfiguredEntity'; }
    
    /** @return {Set} */
    get parentSymbols() { return this._parentSymbols; }
    
    /**
     * Get an array of the Fields we're going to encode in JSON
     * @return {Set<string>}
     */
    get jsonFields() {
        return new Set(['smID', '?inherits']);
    }
    
    /**
     * This is the name as it was used when we were initially configuring whatever this was.
     */
    get configName() {
        return this.getOriginalConfiguration().configName;
    }
    
    get inheritables() {
        return this.getInheritables();
    }
    
    static _mergeConfigurations(...configurations: {}[]) {
        return merge.all(configurations)
    }
    
    initialize(config) {
        let inherits                     = config.inherits;
        const completeInitialInheritance = this._completeInitialInheritance(inherits);
        return super.initialize(config)
                    .then(i => completeInitialInheritance)
                    .then(i => this.configure(config))
                    .then(i => this)
    }
    
    toJSON__inherits() {
        const inherits = new Set;
        this._parents.forEach((item: ConfiguredEntity) => inherits.add(item.smID));
        return [...inherits];
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
    
    toJSON() {
        const jsonFields = this.jsonFields;
        const json_obj   = {};
        jsonFields.forEach(fieldName => {
            let is_optional = fieldName[0] === '?';
            if (fieldName[0] === '?') fieldName = fieldName.substr(1);
            
            const fn_name = `toJSON__${fieldName}`;
            let item      = this[fn_name] ? (this[fn_name]()) : this[fieldName];
            if (item instanceof Map) item = mapToObj(item);
            
            if (is_optional && item instanceof Array && !item.length) return;
            
            json_obj[fieldName] = item;
        });
        return json_obj;
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
                           this._parents.add(parent);
                           // Only inherit what the parent is willing to give
                           const newConfiguration = this.constructor._mergeConfigurations(parent.inheritables,
                                                                                          this.getOriginalConfiguration);
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