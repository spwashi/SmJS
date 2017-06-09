/**
 * Created by Sam Washington on 5/20/17.
 */
const Std = require('../std/').Std;
/**
 * @class ConfiguredEntity
 */
export default class ConfiguredEntity extends Std {
    static get name() {return 'ConfiguredEntity'; }
    
    get name() {return this._name;}
    
    /** @return {Set} */
    get parents() { return this._parentSymbols; }
    
    constructor(name, config = {}) {
        if (!config && typeof name === 'object') config = name;
        name = config.name = config.name || name;
        super(name);
        this._config        = config;
        this._parentSymbols = new Set;
        const finish        = _ => this.complete(ConfiguredEntity.name);
        
        let configPromise = (this._parentPromise || Promise.resolve())
            .then(i => this.configure(config));
        
        // Inherit the necessary things before marking this as "complete"
        if (config.follows) {
            const follows          = Array.isArray(config.follows) ? config.follows : [config.follows];
            const INHERIT          = Std.EVENTS.item('inherit');
            const inheritedFollows = follows.map(item => this.inherit(item));
            
            this._parentPromise =
                configPromise
                    .then(i => this.send(this.EVENTS.item(INHERIT.BEGIN).STATIC, this))
                    .then(i => Promise.all(inheritedFollows))
                    .then(i => this.send(this.EVENTS.item(INHERIT.COMPLETE).STATIC, this))
                    .then(finish);
        } else {
            this._parentPromise = configPromise.then(i => finish());
        }
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