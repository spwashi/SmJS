import {Configuration, EVENT__CONFIG} from "../../configuration/configuration";
import ModelConfiguration from "../entities/model/configuration";
import {Model} from "../entities/model/model";
import {Configurable} from "../../configuration/types";

export class ApplicationConfiguration extends Configuration {
    handlers = {
        models:    (modelConfigObj, owner) => {
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
        routes:    (routes, owner) => {
            return owner._routes = routes || {};
        },
        name:      (name, app) => app._name = name,
        namespace: (namespace, app) => app._namespace = namespace,
        domain:    (domain, app) => app._domain = domain,
        urlPath:   (urlPath, app) => {
            if (typeof urlPath !== "string" || !urlPath.length) return;
        
            return app._urlPath = urlPath;
        },
    }
}

export class Application implements Configurable {
    _models: {};
    _routes: {};
    _name: string;
    _namespace: string;
    _urlPath: string;
    _domain: string;
    
    constructor() {
        this._models = {};
    }
    
    get name() {return this._name}
    
    get namespace() {return this._namespace}
    
    get urlPath() {return this._urlPath}
    
    get domain() {return this._domain}
    
    get url() {return this._domain + (this.urlPath ? `/${this.urlPath}/` : '')}
    
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
        this._models && (obj.models = this._models);
        this._urlPath && (obj.urlPath = this._urlPath);
        this._domain && (obj.domain = this._domain);
        this._domain && (obj.url = this.url);
        return obj
    }
}