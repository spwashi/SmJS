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
    });
    it('Can configure Paths', () => {
        const app       = new Application;
        const paths     = {'public': '/var/www/static/main'};
        const appConfig = new ApplicationConfiguration({paths});
        appConfig.configure(app)
                 .then(i => {
                     console.log(JSON.stringify(app, '\t', 2));
                 })
                 .catch(e => console.error(e));
    })
});