import {createIdentityManager} from "../../../identity/components/identity";
import {IdentityManager} from "../../../identity/types";

export const entityIdentity: IdentityManager = createIdentityManager('Entity');
export default entityIdentity;
