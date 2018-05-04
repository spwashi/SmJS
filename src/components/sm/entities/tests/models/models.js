import * as _ from './_';
import * as user from './user';
import * as file from './file';
import * as password from './password';
import * as person_email_map from './person/person_email_map';
import * as person from './person';
import * as email from './email';
import * as project from './project';

export const models = {
    _,
    file,
    password,
    project,
    person_email_map,
    person,
    user,
    email,
};

export default models;