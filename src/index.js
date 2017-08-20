import std from "./std";
import _config from "./_config";
import config from "./entities";
import errors from "./errors";
import util from "./util";

export {errors}
export {util}
export {_config}
export {std} ;
export {config} ;
export {Sm};

/**
 * @module Sm
 * @name Sm
 * @alias Sm
 */
const Sm = {_config, std, config, errors, util};

export default Sm;