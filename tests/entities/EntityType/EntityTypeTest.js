import {describe, it} from "mocha";
import {Sm} from "../../Sm"

import {expect} from "chai";

describe('EntityType', () => {
    const Std            = Sm.std.Std;
    const EntityType     = Sm.entities.EntityType;
    const EntityProperty = Sm.entities.EntityType.EntityTypeProperty;
    const testEntity     = EntityType.init('testEntity').initializingObject;
    
    it('exists', () => {
        expect(testEntity.Symbol).to.be.a('symbol');
        expect(testEntity.Symbol.toString()).to.equal(Symbol(`[${EntityType.name}]testEntity`).toString())
    });
    
    it('Can resolve properties', done => {
        const entityName     = '[EntityType]testResolveProperties';
        const _property_name = 'test_property';
        
        const entity =
                  EntityType.init('testResolveProperties', {properties: {test_property: {}}})
                      .initializingObject;
        Std.resolve(`${entityName}|${_property_name}`)
           .then(i => {
               let [event, property] = i;
    
               // [EntityTypeProperty]{[EntityType]testResolveProperties}test_property
               expect(entity.properties.get(`[EntityTypeProperty]\{${entityName}}${_property_name}`)).to.equal(property);
               expect(property).to.be.instanceof(EntityProperty);
            
               return entity.resolve(_property_name).then(prop => done());
           })
           .catch(e => console.log(e));
    });
    
});