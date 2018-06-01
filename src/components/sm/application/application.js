import {Configuration} from "../../configuration/configuration";
import ModelConfiguration from "../entities/model/configuration";
import {Model} from "../entities/model/model";
import {Entity} from "../entities/entity/entity";
import {Configurable} from "../../configuration/types";
import type {ConfigurationSession} from "../../configuration/types";
import EntityConfiguration from "../entities/entity/configuration";
import {makeSmEntity} from "../entities/smEntity";
import modelIdentity from "../entities/model/identity";
import applicationIdentity from "./identity";

let batchConfigureSmEntity = function ([SmEntityConfiguration, SmEntityProto],
                                       smEntityConfig,
                                       configurationSession,
                                       onConfigured) {
    const config          = smEntityConfig;
    const allInitializing = Object.keys(config)
                                  .map(key => {
                                      const smEntityName: string  = key;
                                      const configurationObj      = config[key];
                                      configurationObj.name       = smEntityName;
                                      const smEntityConfiguration = new SmEntityConfiguration(configurationObj, configurationSession);
                                      return smEntityConfiguration.configure(new SmEntityProto())
                                                                  .then(smEntity => onConfigured(smEntityName, smEntity));
                                  });
    return Promise.all(allInitializing)
};

export class ApplicationConfiguration extends Configuration {
    handlers = {
        models:      (smEntityConfig = {}, owner: Application, configurationSession: ConfigurationSession) => {
            const onConfigured = (smEntityName, smEntity) => {
                owner._models[smEntityName]      = smEntity;
                owner._models[smEntityName].name = owner._models[smEntityName].name || smEntityName;
            };
            return batchConfigureSmEntity([ModelConfiguration, Model],
                                          smEntityConfig,
                                          configurationSession,
                                          onConfigured);
        },
        entities:    (smEntityConfig = {}, owner: Application, configurationSession: ConfigurationSession) => {
            const onConfigured = (smEntityName, smEntity) => {owner._entities[smEntityName] = smEntity;};
            return batchConfigureSmEntity([EntityConfiguration, Entity],
                                          smEntityConfig,
                                          configurationSession,
                                          onConfigured);
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
        urls:        (urls, owner: Application) => {
            urls = Object.assign({},
                                 {src: null, config: null, 'public': null},
                                 (urls || {})
            );
            return owner._urls = urls;
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
    _entities: {};
    _routes: {};
    _paths: {
        'public': string
    };
    _urls: {
        'public': string
    };
    _name: string;
    _namespace: string;
    _baseUrlPath: string;
    _rootUrl: string;
    
    constructor() {
        this._models   = {};
        this._entities = {};
    }
    
    get name() {return this._name}
    
    get namespace() {return this._namespace}
    
    get paths() {return this._paths}
    
    get baseUrlPath() {return this._baseUrlPath}
    
    get baseUrl() {return this._rootUrl + (this.baseUrlPath ? `/${this.baseUrlPath}` : '')}
    
    get urls() {
        return Object.assign({},
                             this._urls,
                             {
                                 root: this._rootUrl,
                                 base: this.baseUrl
                             }
        )
    }
    
    get models() {
        return this._models;
    }
    
    get entities() {
        return this._entities;
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
        this._entities && (obj.entities = this._entities);
        this._baseUrlPath && (obj.urlPath = this._baseUrlPath);
        obj.urls = this.urls;
        return obj
    }
}

makeSmEntity(Application, applicationIdentity);