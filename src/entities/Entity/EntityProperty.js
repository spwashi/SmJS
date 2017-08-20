/**
 * @class Entity
 */
import Property from "../Property";

/**
 * @name Entity
 * @class Entity
 * @extends Property
 */
export default class EntityProperty extends Property {
    static get smID() {return 'EntityProperty'; }
    
    get jsonFields() {
        return new Set([...super.jsonFields])
    }
}