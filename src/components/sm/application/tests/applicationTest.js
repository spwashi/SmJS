import {describe, it} from "mocha";
import {Application, ApplicationConfiguration} from "../application";
import models from "./models/models";

describe('application', () => {
    it('Can configure Models', () => {
        const app       = new Application;
        const appConfig = new ApplicationConfiguration({models});
        appConfig.configure(app)
                 .then(i => {
                     console.log(JSON.stringify(app, '\t', 2));
                 })
                 .catch(e => console.error(e));
    })
});