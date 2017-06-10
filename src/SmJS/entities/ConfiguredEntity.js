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
    
    get name() {return this._name;}
    
    /** @return {Set} */
    get parents() { return this._parentSymbols; }
    
    _storeOriginalConfiguration(original_config) {
        this._originalConfig = _.cloneDeep(original_config);
        return this;
    }
    
    getOriginalConfiguration() {
        return this._originalConfig;
    }
    
    /**
     * Complete the initialization (shortcut for complete)
     * @return {Promise}
     * @private
     */
    _finishInit() {
        return this.complete(ConfiguredEntity.name);
    }
    
    _completeInitialInheritance(parent_identifiers) {
        parent_identifiers     = Array.isArray(parent_identifiers) ? parent_identifiers : [parent_identifiers];
        const INHERIT          = Std.EVENTS.item('inherit');
        const inheritedFollows = parent_identifiers.map(item => this.inherit(item));
        return this.send(this.EVENTS.item(INHERIT.BEGIN).STATIC, this)
                   .then(i => Promise.all(inheritedFollows))
                   .then(i => this.send(this.EVENTS.item(INHERIT.COMPLETE).STATIC, this))
    }
    
    constructor(name, config = {}) {
        if (!config && typeof name === 'object') config = name;
        name = config.name = config.name || name;
        super(name);
        this._parentSymbols = new Set;
        this._storeOriginalConfiguration(config);
        let inherits        = config.inherits;
        this._parentPromise = this._parentPromise
                                  .then(i => this.configure(config))
                                  .then(i => this._completeInitialInheritance(inherits))
                                  .then(i => this._finishInit());
    }
    
    configure(properties) {
        let promises = [];
        for (let property_name in properties) {
            if (!properties.hasOwnProperty(property_name)) continue;
            const fn_name = 'set_' + property_name;
            const fn_     = this[fn_name];
            if (typeof this[fn_name] === 'function') promises.push(this[fn_name](properties[property_name]));
            else promises.push(Promise.resolve(fn_name));
        }
        return Promise.all(promises);
    }
    
    getInheritables() {
        return {};
    }
    
    /**
     *
     * @param item
     * @return {*|Promise.<this>}
     */
    inherit(item) {
        if (!item) return Promise.resolve([]);
        return this.constructor.resolve(item).then(
            (result) => {
                /** @type {Event} event */
                let [event, parent] = result;
                /** @type {ConfiguredEntity} parent */
                if (!(parent instanceof this.constructor)) {
                    throw new Error('Cannot accept ' + (String(parent)));
                }
                this._parentSymbols.add(parent.Symbol);
                return this.configure(parent.getInheritables());
            });
    }
}