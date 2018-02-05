import {describe, it} from "mocha";
import {expect} from "chai";
import {Entity} from "./entity"
import EntityConfiguration from './configuration'

describe('Entity', () => {
    it('exists', () => {
        const entity = new Entity;
        expect(entity).to.be.instanceOf(Entity);
        
        console.log(entity.identity)
    });
    
    it('Can configure properties', () => {
        const config = new EntityConfiguration({
                                                   properties: {
                                                       title: {
                                                           unique:    true,
                                                           datatypes: ['int', 'string']
                                                       }
                                                   }
                                               });
        config.configure(new Entity)
              .then(result => {
                  console.log(JSON.stringify(result, ' ', 5));
              });
    })
});