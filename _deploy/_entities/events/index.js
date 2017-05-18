/**
 * Created by Sam Washington on 5/6/17.
 */
import {SYMBOL, symbols} from "../symbols/index";
import StdEventDescriptor from "./StdEventDescriptor";
import StdEventEmitter from "../Std";

export const $Events$ = symbols.register('EVENTS');
export const EVENTS   = $Events$[SYMBOL];

const init     = $Events$.register('init').init
                         .register(['_class', 'identity', 'references', 'self']);
const inherit  = $Events$.register('inherit').inherit
                         .register(['references']);
const add      = $Events$.register('add').add;
const complete = $Events$.register('complete').complete
                         .register(['_class', 'identity', 'references', 'self']);

export default {StdEventDescriptor, StdEventEmitter};