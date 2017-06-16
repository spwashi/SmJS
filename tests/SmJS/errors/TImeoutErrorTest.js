import {describe, it} from "mocha";
import {SMJS_PATH} from "../paths";
const Sm     = require(SMJS_PATH);
const expect = require('chai').expect;

describe('TimeoutError', () => {
    const TimeoutError = Sm.errors.TimeoutError;
    it('Can throw an error with a symbol and a time', () => {
        const thro = i => {
            try {
                throw new TimeoutError("This is a test", Symbol('This is a symbol'), 50);
            } catch (e) {
                console.log(e);
                throw e;
            }
        };
        expect(thro).to.throw(TimeoutError);
    });
});