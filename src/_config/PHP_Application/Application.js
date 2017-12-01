import {ApplicationConfiguration as AppConfiguration} from "./Configuration";
import ConfiguredEntity from "../../entities/ConfiguredEntity";

class PHP_Application extends ConfiguredEntity {
    static Configuration = AppConfiguration;
           _name;
           _controller: { namespace: string };
           _urls: {
               _: string,
        
               resources: {
                   css: string,
                   js: string
               }
           }             = {};
           _paths: {
               app: string,
               config: string,
               models: string,
               routes: string
           }             = {};
           _namespace;
    
    get namespace() {return ((this._namespace + '\\') || '\\');}
    
    get name() {return this._name;}
    
    get jsonFields() {
        return new Set(['name', 'namespace', 'controller', 'paths'])
    }
    
    configure(config: ConfiguredEntity._config | string) {
        if (typeof config !== 'string') {
            return super.configure(config);
        }
        
        const configFileIsJSON = filepath => filepath.split('.').reverse().shift() === 'json';
        
        const configFilePath = config;
        
        if (configFileIsJSON(configFilePath)) {
            const fs                = require('fs');
            const stripJsonComments = require("strip-json-comments");
            
            let readConfigFile = resolve => {
                const onReadFile = (err, text) => {
                    let getConfigObjFromString = (str) => JSON.parse(stripJsonComments(str));
                    let configObj              = getConfigObjFromString(text);
                    let configResult           = this.configure(configObj);
                    resolve(configResult);
                };
                return fs.readFile(configFilePath, 'utf8', onReadFile);
            };
            
            return new Promise(readConfigFile);
        }
    }
    
    toJSON__controller() {
        return Object.assign({}, this._controller, {namespace: this.namespace + this._controller.namespace + '\\'});
    }
    
    toJSON__paths() {
        return this._paths;
    }
}

export {PHP_Application}