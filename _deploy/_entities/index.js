import "babel-polyfill";
import "babel-core";
import "./_config/init";
import "./_debug";
import Model from "./Model";
import entities from "./_config/index";
import {_entries} from "./util/index";
import {SYMBOL} from "./symbols/index";
import {EVENTS} from "./events/index";

let _Models   = {};
let _Promises = [];
for (let [model_name, model] of _entries(entities.models)) {
    const M = Model.init(Object.assign({_name: model_name}, model))
                   .then_do(Model => _Models[model_name] = (Model.Events._emitted_events));
    
    const complete = M.when(Model[EVENTS].complete.self[SYMBOL]);
    _Promises.push(complete);
}

Promise.all(_Promises);