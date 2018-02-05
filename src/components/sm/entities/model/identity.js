import {createIdentityManager} from "../../../identity/components/identity";
import {IdentityManager} from "../../../identity/types";

export const modelIdentity: IdentityManager = createIdentityManager('Model');
export default modelIdentity;
