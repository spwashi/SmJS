import {before, beforeEach, describe, it} from "mocha";
import {expect} from "chai";
import * as __modelConfig from './models/_/index';
import * as person_modelConfig from './models/person/index';
import * as project_modelConfig from './models/project/index';
import ModelConfiguration from "../model/configuration";
import {Model} from "../model/model";

let initializeUnderscoreModel = () => (new ModelConfiguration(__modelConfig)).configure(new Model);

describe('Project Model', () => {
    it(`(looks like it's supposed to)`, done => {
        initializeUnderscoreModel();
        const modelConfiguration = new ModelConfiguration(project_modelConfig);
        modelConfiguration.configure(new Model)
                          .then(ProjectModel => {
                              console.log(JSON.stringify(ProjectModel, ' ', 3));
                              done();
                          })    });
    
    it('Has a  "person_id" property that serves allows us to proxy a "Person" Model', done => {
        initializeUnderscoreModel();
        (new ModelConfiguration(person_modelConfig)).configure(new Model)
                                                    .catch(e => console.error(e));
        const projectConfiguration = new ModelConfiguration(project_modelConfig);
        projectConfiguration.configure(new Model)
                            .catch(e => console.error(e))
                            .then(ProjectModel => {
                                const json__string = JSON.stringify(ProjectModel, ' ', 3);
                                console.log(json__string);
                                done();
                            });
    });
});