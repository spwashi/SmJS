/**
 * An object that is meant to contain information about properties as they exist in this context
 * @class PropertyMetaContainer
 * @extends Std
 */
import {Property} from "./property";
import {SM_ID} from "../../identification";
import {CONFIGURATION} from "../../../configuration/configuration";

export class PropertyMeta {
    _indices: { [name: string]: Set | Map };
    
    constructor() {
        this._indices = {};
        
        this.createIndexType('primary', Set);
        this.createIndexType('unique', Map);
    }
    
    isEmpty() {
        const _indices = this._indices;
        for (let key in _indices) {
            if (!_indices.hasOwnProperty(key)) continue;
            /** @type Map|Set */
            let index = _indices[key];
            if (index.size) return false;
        }
        
        return true;
    }
    
    createIndexType(name, ContainerType: typeof Map | typeof Set) {
        return this._indices[name] = new ContainerType;
    }
    
    /**
     * These functions require that a Set of properties is used.
     * @param propertySet
     * @private
     */
    _enforceIsPropertySet(propertySet) {
        if (propertySet instanceof Property) propertySet = new Set([propertySet]);
        
        if (!(propertySet instanceof Set)) throw new Error("Invalid argument Type - must be Property or Set");
        return propertySet;
    }
    
    /**
     *
     * @param index
     * @param {Property}        property
     * @return {Set|Map|boolean}
     */
    findInIndex(index: string, property) {
        const keySet = this.getIndex(index);
        // default behavior- return the keyset
        if (property === null || typeof property === "undefined") return keySet;
        
        if (keySet instanceof Map) {
            let matchingKeysets = new Map;
            keySet.forEach((set, name, map) => {
                const _keySet = this.findInIndex(property, set);
                
                if (_keySet instanceof Set) {
                    matchingKeysets.set(name, _keySet);
                }
            });
            if (!matchingKeysets.size) return false;
            return matchingKeysets;
        }
        
        //If we pass in a property, interpret this as "get PrimaryKey where property = ___"
        return keySet.has(property) ? keySet : false;
    }
    
    getIndex(index: string) {
        return this._indices[index] || this.createIndexType(index, Set);
    }
    
    _toJSON__set(set) {
        return [...set].map(property => property[SM_ID]);
    }
    
    toJSON__map(map) {
        const unique         = {};
        const selfUniqueKeys = map;
        
        if (!selfUniqueKeys) return null;
        
        selfUniqueKeys.forEach((item, index) => {
            unique[index] = [...item].map(property => property[SM_ID]);
        });
        return unique
    }
    
    toJSON() {
        let ret = {};
        for (let index in this._indices) {
            if (!this._indices.hasOwnProperty(index)) continue;
            /** @type Map|Set */
            const indexIterable = this._indices[index];
            if (!indexIterable.size) continue;
            if (indexIterable instanceof Map) {
                ret[index] = this.toJSON__map(indexIterable)
            } else if (indexIterable instanceof Set) {
                ret[index] = this._toJSON__set(indexIterable)
            }
        }
        
        return ret;
    }
    
    /**
     * Add what should be a set of properties to a pre-existing set (if that exists)
     * @param keySet
     * @param propertySet
     * @return {Set}
     * @private
     */
    _mergePropertySets(keySet, propertySet) {
        keySet = keySet || [];
        return new Set([...keySet, ...this._enforceIsPropertySet(propertySet)]);
    }
    
    /**
     * Set the properties that are going to act as the Primary Key.
     *
     * @param index
     * @param {Set|Property} propertySet A Property or Set of properties that are going to be used as the Primary Key.
     * @return {PropertyMetaContainer}
     */
    _addToSetIndex(index, propertySet) {
        !this._indices[index] && this.createIndexType(index, Set);
        
        if (!(this._indices[index] instanceof Set)) {
            throw new Error("Cannot add to Set index-- wrong type given", this._indices[index]);
        }
        
        return this._indices[index] = this._mergePropertySets(this._indices[index], propertySet);
    }
    
    /**
     * Add a Property or a Set of properties to act as a Unique Key under a particular unique key name.
     * The unique key name is used for identification purposes. In instances when this PropertyMetaContainer is being
     * used to configure a TableDataSource, this would be useful in naming those keys.
     *
     * @param index
     * @param {string}  keyName     The name of the Key to add
     * @param {Set|Property}     propertySet The property or Set of properties thar are going to be used as the unique key.
     */
    _addToMapIndex(index, keyName, propertySet) {
        !this._indices[index] && this.createIndexType(index, Map);
        
        if (!(this._indices[index] instanceof Map)) {
            throw new Error("Cannot add to Map index-- wrong type given", this._indices[index]);
        }
        
        // Add the Set to the others
        const keySet = this._mergePropertySets(this._indices[index].get(keyName),
                                               propertySet);
        this._indices[index].set(keyName, keySet);
    }
    
    incorporateProperty(property: Property): Property {
        return property;
    }
}
