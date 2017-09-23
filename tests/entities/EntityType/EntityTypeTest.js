import {describe, it} from "mocha";
import {Sm} from "../../Sm"

import {expect} from "chai";

describe('EntityType', () => {
    const Std                = Sm.std.Std;
    const EntityType         = Sm.entities.EntityType;
    const EntityTypeProperty = Sm.entities.EntityType.EntityTypeProperty;
    const testEntity         = EntityType.init('testEntity').initializingObject;
    
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
               expect(property).to.be.instanceof(EntityTypeProperty);
            
               return entity.resolve(_property_name).then(prop => done());
           })
           .catch(e => console.log(e));
    });
    
    it('Can configure Models', () => {
        const schema_config = {};
        
        let entity_config: Sm.entities.EntityType.entity_type_config;
        
        entity_config = {
            _id:     'student',
            context: {
                'instructional_facility': '[Entity]instructional_facility'
            },
            models:  {
                person:  {
                    model:    '[Model]person_ccm',
                    identity: 'id'
                },
                student: {
                    model:    '[Model]student_ccm',
                    identity: {
                        person_id:                 '[Model]person_ccm',
                        instructional_facility_id: '[Context]instructional_facility|[Property]id'
                    },
                }
            }
        };
        EntityType.init(entity_config);
    })
    
});