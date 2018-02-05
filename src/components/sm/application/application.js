import {Configuration, EVENT__CONFIG} from "../../configuration/configuration";
import ModelConfiguration from "../entities/model/configuration";
import {Model} from "../entities/model/model";
import {Configurable} from "../../configuration/types";
import {ITEM_CONFIGURED__EVENT} from "../entities/model/events";

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
        },
        routes: (routes, owner) => {
            return owner._routes = routes || {};
        }
    }
}

export class Application implements Configurable {
    _models: {};
    _routes: {};
    
    constructor() {
        this._models = {};
    }
    
    get models() {
        return this._models;
    }
    
    get routes() {
        return this._routes;
    }
    
    toJSON() {
        return {
            models: this._models,
            routes: this._routes
    
        }
    }
}