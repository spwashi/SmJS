import {describe, it} from "mocha";
import {Sm} from "../../Sm"

import {expect} from "chai";

describe('Entity', () => {
    const Std            = Sm.std.Std;
    const Entity         = Sm.config.Entity;
    const EntityProperty = Sm.config.Entity.EntityProperty;
    const testEntity     = Entity.init('testEntity').initializingObject;
    
    it('exists', () => {
        expect(testEntity.Symbol).to.be.a('symbol');
        expect(testEntity.Symbol.toString()).to.equal(Symbol(`[${Entity.name}]testEntity`).toString())
    });
    
    it('Can resolve properties', done => {
        const entityName     = '[Entity]testResolveProperties';
        const _property_name = 'test_property';
        
        const entity =
                  Entity.init('testResolveProperties', {properties: {test_property: {}}})
                      .initializingObject;
        Std.resolve(`${entityName}|${_property_name}`)
           .then(i => {
               let [event, property] = i;
            
               // [EntityProperty]{[Entity]testResolveProperties}test_property
               expect(entity.properties.get(`[EntityProperty]\{${entityName}}${_property_name}`)).to.equal(property);
               expect(property).to.be.instanceof(EntityProperty);
            
               return entity.resolve(_property_name).then(prop => done());
           })
           .catch(e => console.log(e));
    });
    
});