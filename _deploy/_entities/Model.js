/**
 * Created by Sam Washington on 5/4/17.
 */
import Property from "./Property";
import ReferenceResolver from "./ReferenceResolver";
import {_entries} from "./util/index";
import {EVENTS} from "./events/index";
import {SYMBOL, symbols} from "./symbols/index";

let has_been_called = false;
/**
 * @class Model
 * @extends ReferenceResolver
 */
class Model extends ReferenceResolver {
    static _initClass() {
        has_been_called = true;
        if (super._initClass() === null) return null;
        
        //define the properties that we are going to be using in this class
        this[EVENTS].item('add').item('property');
        this[EVENTS].item('init').item('properties');
        
        return true;
    }
    
    resolve(name) {
        if (typeof name !== 'string') return Promise.reject("Can't resolve non-strings");
        if (name[0] === '{' && name[name.length - 1] === '}') {
            let p_name = name.substr(1, name.length - 2);
            return this.when_property(p_name);
        }
    }
    
    inherit(item) {
        return super.inherit(item);
    }
    
    static get defaults() {
        return {
            properties: null
        }
    }
    
    createIdentity(_config) {
        //done here before we set the properties of this
        this._Properties = new Set;
        super.createIdentity(_config)
    }
    
    when_property(property_name) {
        return this.Events.when(this.static[EVENTS].add.property.item(property_name)[SYMBOL]);
    }
    
    constructor(_config = {}) {
        _config.original_properties = _config.properties = _config.properties || {};
        delete _config.properties;
        super(_config);
        //require that the first properties get initialized
        const _completion_stds = [];
        
        _completion_stds.push(this.static[EVENTS].init.properties[symbols.modifiers.FIRST][SYMBOL]);
        
        if (Array.isArray(_config.follows) && _config.follows.length) {
            _completion_stds.push(this.static[EVENTS].inherit.references[symbols.modifiers.COMPLETE][SYMBOL]);
        }
        
        this.Events
            .when(_completion_stds)
            .then(_ => this._complete());
    }
    
    set original_properties(properties) {
        this._initProperties(properties)
            .then(_ => this.Events.emitOnce(this.static[EVENTS].init.properties[symbols.modifiers.FIRST][SYMBOL]))
    }
    
    set properties(properties) {
        if (properties === null) return;
        this._initProperties(properties)
    }
    
    get properties() {
        let properties = {};
        for (let _property of this._Properties) {
            if (!(_property instanceof Property)) continue;
            properties[_property._name] = _property;
        }
        return properties;
    }
    
    _initProperties(properties) {
        this.Events.emit(this.static[EVENTS].init.properties[symbols.modifiers.BEGIN][SYMBOL], properties);
        let _Promises = [];
        if (!properties || (typeof properties !== "object")) return Promise.resolve();
        for (let [property_name, property] of _entries(properties)) {
            let _prop_config = {_name: property_name, _model_identity: this._identity};
            let Prop         = Property.init(_prop_config)
                                       .inherit(property)
                                       .when(this.static[EVENTS].complete.self[SYMBOL])
                                       .then(Property => {
                                           this.Events.emitOnce(this.static[EVENTS].add.item(property_name)[SYMBOL],
                                                                Property);
                                           return Property;
                                       })
                                       .then(Property => this._Properties.add(Property));
            _Promises.push(Prop);
        }
        return Promise.all(_Promises);
    }
}
Model._object_type = 'model';
export default Model;