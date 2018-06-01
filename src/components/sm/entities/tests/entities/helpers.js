import Sm from "../../../../sm/";

const {Model, Entity} = Sm;
export {Model, Entity};
export const normalizeContexts = obj => {
    return Object.entries(obj)
                 .reduce(
                     (all, entry) => {
                         const [name, context] = entry;
                         all[name]             = {name, ...context};
                         return all;
                     }, {});
};
export const CONTEXT_TYPES     = {SCENE: 'scene', IDENTITY: 'identity'};