import {Identifiable} from "../../identity/types";
import EventManager from "../../event/eventManager";
import {SM_ID} from "../identification";
import Identity from "../../identity/components/identity";

export interface SmEntity extends Identifiable {
    [SM_ID]: Identity;
    
    static [SM_ID]: Identity;
    
    static eventManager: EventManager;
    static events: smEntityEventConfig;
    
    static identify (name: string): Identity => {};
    
    static init(name: string | Identity): Promise<SmEntity>;
}

export type smEntityEventConfig = { CONFIG_END: Identity | undefined };