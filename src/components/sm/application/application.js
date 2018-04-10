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
            paths = Object.assign({},
                                  {src: null, config: null, 'public': null},
                                  (paths || {})
            );
            return owner._paths = paths;
        },
        name:        (name, app: Application) => app._name = name,
        namespace:   (namespace, app: Application) => app._namespace = namespace,
        rootUrl:     (domain, app: Application) => app._rootUrl = domain,
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
    _rootUrl: string;
    
    constructor() {
        this._models = {};
    }
    
    get name() {return this._name}
    
    get namespace() {return this._namespace}
    
    get paths() {return this._paths}
    
    get baseUrlPath() {return this._baseUrlPath}
    
    get baseUrl() {return this._rootUrl + (this.baseUrlPath ? `/${this.baseUrlPath}` : '')}
    
    get urls() {
        return {
            root: this._rootUrl,
            base: this.baseUrl
        }
    }
    
    get models() {
        return this._models;
    }
    
    get routes() {
        return this._routes;
    }
    
    toJSON__public() {
        return {
            name:         this.name,
            appDomain:    this._rootUrl,
            appUrl:       this.baseUrl,
            appPath:      this.baseUrlPath,
            appPublicUrl: this.urls.public
        }
    }
    
    toJSON() {
        const obj = {};
        this._name && (obj.name = this._name);
        this._namespace && (obj.namespace = this._namespace);
        this._routes && (obj.routes = this._routes);
        this._paths && (obj.paths = this._paths);
        this._models && (obj.models = this._models);
        this._baseUrlPath && (obj.urlPath = this._baseUrlPath);
        obj.urls = this.urls;
        return obj
    }
}