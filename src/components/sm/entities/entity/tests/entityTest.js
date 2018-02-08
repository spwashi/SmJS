import {describe, it} from "mocha";
import {expect} from "chai";
import {Entity} from "../entity"
import EntityConfiguration from '../configuration'
import person from "./person";

describe('Entity', () => {
    it('exists', () => {
        const entity = new Entity;
        expect(entity).to.be.instanceOf(Entity);
    });
    it('Can configure properties', () => {
        const personConfig = new EntityConfiguration(person);
        personConfig.configure(new Entity)
                    .then(result => {
                        console.log(JSON.stringify(result, ' ', 5));
                    })
                    .catch(e => console.log(e));
    })
});