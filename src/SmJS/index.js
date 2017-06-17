import std from "./std";
import _config from "./_config";
import entities from "./entities";
import errors from "./errors";
import util from "./util";
export {errors}
export {util}
export {_config}
export {std} ;
export {entities} ;
export {Sm};

/**
 * @module Sm
 * @name Sm
 * @alias Sm
 */
const Sm = {_config, std, entities, errors, util};

export default Sm;