import {describe, it} from "mocha";
const expect  = require('chai').expect;
const _deploy = require('../../../_deploy');

describe('Source', () => {
    const Source     = _deploy.entities.Source;
    const testSource = new Source('testSource');
    it('exists', () => {
        expect(testSource.Symbol).to.be.a('symbol');
        expect(testSource.Symbol.toString()).to.equal(Symbol(`[${Source.name}].testSource`).toString())
    });
});