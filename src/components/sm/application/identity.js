import {createIdentityManager} from "../../identity/components/identity";
import {IdentityManager} from "../../identity/types";

export const applicationIdentity: IdentityManager = createIdentityManager('Application');
export default applicationIdentity;
