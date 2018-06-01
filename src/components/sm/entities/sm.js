import {parseSmID} from "../utility";

export const Sm      = function () {};
Sm.getManagerForSmID = function (smID) {
    const {manager} = parseSmID(`${smID}`);
    if (manager && Sm[manager] && Sm[manager].identify) {
        return Sm[manager];
    }
    throw new Error('Could not resolve manager for ' + smID);
};
Sm.identify          = (smID: string | Identity) => {
    const SmEntityManager = Sm.getManagerForSmID(smID);
    return SmEntityManager.identify(smID);
};
Sm.init              = (smID: string | Identity) => {
    const SmEntityManager = Sm.getManagerForSmID(smID);
    return SmEntityManager.init(smID);
};