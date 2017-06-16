import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const Sm     = require(SMJS_PATH);
const expect = require('chai').expect;

describe('Errors', () => {
    const GenericError = Sm.errors.GenericError;
    it('Can throw an error with a symbol', () => {
        const thro = i => {
            try {
                console.log(GenericError);
                throw new GenericError("This is a test", Symbol('This is a symbol'));
            } catch (e) {
                console.log(e);
                throw e;
            }
        };
        expect(thro).to.throw(GenericError);
    });
});