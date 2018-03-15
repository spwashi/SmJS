import {describe, it} from "mocha";
import {expect} from "chai";
import * as models from './models'

describe('models', () => {
    const expectedModelNames = [
        'contact',
        'user_account_map',
        'user',
        'person',
        'person_email_map',
        'email'
    ];
    
    it('Has the expected models', () => {
        expectedModelNames.forEach(key => expect(Object.keys(models)).to.contain(key));
        console.log(JSON.stringify(models, ' ', 4));
    });
});