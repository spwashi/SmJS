import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const Sm     = require(SMJS_PATH);
const expect = require('chai').expect;

describe('TypeError', () => {
    const TypeError = Sm.errors.TypeError;
    it('Can throw an error with a symbol and a time', () => {
        const thro = i => {
            throw new TypeError(null, Symbol('This is a symbol'));
        };
        expect(thro).to.throw(TypeError);
    });
});