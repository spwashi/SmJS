import {describe, it} from "mocha";
import {Sm} from "../../Sm"

import {expect} from "chai";

describe('EntityTypeProperty', () => {
    const Std                    = Sm.std.Std;
    const EntityType             = Sm.entities.EntityType;
    const EntityTypeProperty     = Sm.entities.EntityType.EntityTypeProperty;
    const testEntityTypeProperty = EntityTypeProperty.init('testEntity').initializingObject;
    
    it('exists', () => {
        expect(testEntityTypeProperty.Symbol).to.be.a('symbol');
        expect(testEntityTypeProperty.Symbol.toString()).to.equal(Symbol(`[${EntityTypeProperty.name}]testEntity`).toString())
    });
});