import {describe, it} from "mocha";
import {Application, ApplicationConfiguration} from "../application";
import {CONFIGURATION} from "../../../configuration/configuration";

describe('application', () => {
    it('Can configure Models', () => {
        const app       = new Application;
        const appConfig = new ApplicationConfiguration({
                                                           models: {
                                                               user: {
                                                                   properties: {
                                                                       title: {
                                                                           name:      'breatcon',
                                                                           datatypes: ['int', 'string']
                                                                       }
                                                                   }
                                                               }
                                                           }
                                                       });
        appConfig.configure(app)
                 .then(i => {
                     console.log(JSON.stringify(app, '\t', 2));
                 })
                 .catch(e => console.error(e));
    })
});