import "./entities/entity/entity";
import "./application/application";
import "./entities/property/property";
import "./entities/model/model";
import * as identification from './identification'
import {ApplicationConfiguration} from "./application/application";
import {Sm} from "./entities/smEntity";

export default Sm;
Sm.identification           = identification;
Sm.ApplicationConfiguration = ApplicationConfiguration;