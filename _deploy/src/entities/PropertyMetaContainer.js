/**
 * Created by Sam Washington on 6/8/17.
 */

import Property from "./Property";
/**
 * An object that is meant to contain information about Properties as they exist in this context
 */
export default class PropertyMetaContainer {
    constructor() {
        /** @type {Set} Represents the Properties that make up the Primary Key */
        this._primaryKey = new Set;
        /** @type {Map} Represents Sets that represent the Unique Keys */
        this._uniqueKeys = new Map;
    }
    
    /**
     * These functions require that a Set of Properties is used.
     * @param propertySet
     * @private
     */
    _enforceIsPropertySet(propertySet) {
        if (propertySet instanceof Property) propertySet = new Set([propertySet]);
        if (!(propertySet instanceof Set)) throw new Error("Invalid argument Type");
        return propertySet;
    }
    
    /**
     * Set the Properties that are going to act as the Primary Key.
     *
     * @param {Set|Property} propertySet A Property or Set of Properties that are going to be used as the Primary Key.
     * @return {PropertyMetaContainer}
     */
    setPrimaryKeys(propertySet) {
        this._primaryKey = this._enforceIsPropertySet(propertySet);
        return this;
    }
    
    /**
     * Add a Property or a Set of Properties to act as a Unique Key under a particular unique key name.
     * The unique key name is used for identification purposes. In instances when this PropertyMetaContainer is being
     * used to configure a TableDataSource, this would be useful in naming those keys.
     *
     * @param {string}  keyName     The name of the Key to add
     * @param {Set}     propertySet The property or Set of Properties thar are going to be used as the unique key.
     */
    addUniqueKey(keyName, propertySet) {
        this._uniqueKeys.set(keyName, this._enforceIsPropertySet(propertySet));
    }
}