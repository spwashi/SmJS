import entityIdentity from "./identity";
import Identity from "../../../identity/components/identity";
import {getSmEntityEvent} from "../smEntity";

export const ITEM_CONFIGURED__EVENT: Identity = getSmEntityEvent(entityIdentity,
                                                                 'CONFIGURED.ENTITY');