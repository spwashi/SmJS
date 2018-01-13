import {Configuration} from "../../configuration/configuration";
import ModelConfiguration from "../entities/model/configuration";
import {Model} from "../entities/model/model";
import {Configurable} from "../../configuration/types";

export class ApplicationConfiguration extends Configuration {
    handlers = {
        models: (modelConfigObj, owner) => {
            const config          = modelConfigObj;
            const allInitializing = Object.keys(config)
                                          .map(key => {
                                              let modelName: string    = key;
                                              let configurationObj     = config[key];
                                              configurationObj.name    = modelName;
                                              const modelConfiguration = new ModelConfiguration(configurationObj);
                                              return modelConfiguration.configure(new Model)
                                                                       .then(model => {
                                                                           owner._models[modelName] = model;
                                                                       });
                                          });
            return Promise.all(allInitializing)
        }
    }
}

export class Application implements Configurable {
    _models: {};
    
    constructor() {
        this._models = {};
    }
    
    toJSON() {
        return {
            models: this._models
        }
    }
}