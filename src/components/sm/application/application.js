import {Configuration} from "../../configuration/configuration";
import ModelConfiguration from "../entities/model/configuration";
import {Model} from "../entities/model/model";
import {Configurable} from "../../configuration/types";
import type {ConfigurationSession} from "../../configuration/types";

export class ApplicationConfiguration extends Configuration {
    handlers = {
        models:      (modelConfigObj, owner: Application, configurationSession: ConfigurationSession) => {
            const config          = modelConfigObj;
            const allInitializing = Object.keys(config)
                                          .map(key => {
                                              let modelName: string    = key;
                                              let configurationObj     = config[key];
                                              configurationObj.name    = modelName;
                                              const modelConfiguration = new ModelConfiguration(configurationObj, configurationSession);
                                              return modelConfiguration.configure(new Model)
                                                                       .then(model => {
                                                                           owner._models[modelName] = model;
                                                                       });
                                          });
            return Promise.all(allInitializing)
        },
        routes:      (routes, owner: Application) => {
            return owner._routes = routes || {};
        },
        paths:       (paths, owner: Application) => {
            return owner._paths = paths || {};
        },
        name:        (name, app: Application) => app._name = name,
        namespace:   (namespace, app: Application) => app._namespace = namespace,
        baseUrl:     (domain, app: Application) => app._baseUrl = domain,
        baseUrlPath: (urlPath, app: Application) => {
            if (typeof urlPath !== "string" || !urlPath.length) return;
            return app._baseUrlPath = urlPath;
        },
    }
}

export class Application implements Configurable {
    _models: {};
    _routes: {};
    _paths: {
        'public': string
    };
    _name: string;
    _namespace: string;
    _baseUrlPath: string;
    _baseUrl: string;
    
    constructor() {
        this._models = {};
    }
    
    get name() {return this._name}
    
    get namespace() {return this._namespace}
    
    get baseUrlPath() {return this._baseUrlPath}
    
    get baseUrl() {return this._baseUrl + (this.baseUrlPath ? `/${this.baseUrlPath}` : '')}
    
    get models() {
        return this._models;
    }
    
    get routes() {
        return this._routes;
    }
    
    toJSON() {
        const obj = {};
        this._name && (obj.name = this._name);
        this._namespace && (obj.namespace = this._namespace);
        this._routes && (obj.routes = this._routes);
        this._paths && (obj.paths = this._paths);
        this._models && (obj.models = this._models);
        this._baseUrlPath && (obj.urlPath = this._baseUrlPath);
        this._baseUrl && (obj.baseUrl = this.baseUrl);
        return obj
    }
}