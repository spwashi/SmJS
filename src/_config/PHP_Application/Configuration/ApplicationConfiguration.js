import Configuration from "../../../entities/Configuration";
import {PHP_Application} from "../Application";
import ConfiguredEntity from "../../../entities/ConfiguredEntity";
import Model from "../../../entities/Model/Model";
import DataSource from "../../../entities/DataSource/DataSource";

const url                        = require('url');
let LEADING_TRAILING_SLASH_REGEX = /\/$/g;

export class ApplicationConfiguration extends Configuration {
    owner: PHP_Application;
    
    _configure__entity(config, entityPrototype) {
        const allEntitiesInitializing = Object.keys(config)
                                              .map(key => {
                                                  let entityName: string                         = key;
                                                  let configurationObj: ConfiguredEntity._config = config[key];
            
                                                  // set the identifier of this configuration object to be the its index in the configuration object
                                                  configurationObj._id = configurationObj._id || entityName;
            
                                                  // Use the prototype to create an instance of this desired type with its configuration.
                                                  return entityPrototype.init(configurationObj).catch(i => console.error(i))
                                              });
        return Promise.all(allEntitiesInitializing);
    }
    
    configure_models(config) {
        return this._configure__entity(config, Model).then(result => {this.owner._models = result});
    }
    
    configure_sources(config) {
        return this._configure__entity(config, DataSource).then(result => {this.owner._sources = result});
    }
    
    configure_app_name(name) {
        this.owner._name = name;
        return Promise.resolve(name);
    }
    
    configure_namespace(namespace) {
        this.owner._namespace = namespace;
        return Promise.resolve(namespace);
    }
    
    configure_paths(paths) {
        if (typeof paths !== "object") {
            return Promise.reject("Cannot configure non-object _paths");
        }
        
        paths = paths || {};
        
        for (let pathIndex in paths) {
            if (!paths.hasOwnProperty(pathIndex)) continue;
            this.owner._paths[pathIndex] =
                paths[pathIndex].replace('CONFIG_PATH', this.owner._paths.config.replace(LEADING_TRAILING_SLASH_REGEX, ''))
                                .replace('APP_PATH', this.owner._paths.app.replace(LEADING_TRAILING_SLASH_REGEX, ''))
                                .replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
        }
        
        return Promise.resolve(paths);
    }
    
    configure_urls(configURLs) {
        if (typeof configURLs !== "object") {
            return Promise.reject("Cannot configure non-object _paths");
        }
        
        configURLs     = configURLs || {};
        const base_url = configURLs._ || '';
        
        for (let urlIndex in configURLs) {
            if (!configURLs.hasOwnProperty(urlIndex)) continue;
            
            const configURL            = configURLs[urlIndex];
            this.owner._urls[urlIndex] = this._configure__url(configURL);
        }
        
        return Promise.resolve(configURLs);
    }
    
    _configure__url(configURLorPath, _pathReferenceObj: { base_url: string }) {
        if (typeof  configURLorPath === "object") {
            for (let pathName in configURLorPath) {
                if (!configURLorPath.hasOwnProperty(pathName)) continue;
                configURLorPath[pathName] = this._configure__url(configURLorPath[pathName]);
            }
        }
        
        let configURL   = configURLorPath;
        const parsedURL = url.parse(configURLorPath);
        console.log(parsedURL);
        return configURL;
    }
    
    configure_appPath(appPath) {
        this.owner._paths.app =
            appPath.replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
        return Promise.resolve(appPath);
    }
    
    configure_configPath(configPath) {
        this.owner._paths.config =
            configPath.replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
        return Promise.resolve(configPath);
    }
    
    configure_controller(controllerObj) {
        controllerObj           =
            typeof controllerObj === "object" && controllerObj
                ? controllerObj
                : {};
        controllerObj.namespace =
            controllerObj.namespace || "Controller";
        this.owner._controller  = controllerObj;
        return Promise.resolve(controllerObj);
    }
}