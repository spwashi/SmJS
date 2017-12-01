import Configuration from "../../entities/Configuration";
import {PHP_Application} from "./Application";

const url                        = require('url');
let LEADING_TRAILING_SLASH_REGEX = /\/$/g;

export class ApplicationConfiguration extends Configuration {
    owner: PHP_Application;
    
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
    
    _configURL(configURLorPath, _pathReferenceObj: { base_url: string }) {
        if (typeof  configURLorPath === "object") {
            for (let pathName in configURLorPath) {
                if (!configURLorPath.hasOwnProperty(pathName)) continue;
                configURLorPath[pathName] = this._configURL(configURLorPath[pathName]);
            }
        }
        
        let configURL   = configURLorPath;
        const parsedURL = url.parse(configURLorPath);
        console.log(parsedURL);
        return configURL;
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
            this.owner._urls[urlIndex] = this._configURL(configURL);
        }
        
        return Promise.resolve(configURLs);
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