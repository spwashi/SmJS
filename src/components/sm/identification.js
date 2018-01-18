import {configurationHandler} from "../configuration/configuration";

export const SM_ID = Symbol('PROPERTY NAME');

export const createName                                                 = (name) => name || Math.random().toString(36).substr(4, 6);
createName.ofType                                                       = (type, name) => `[${type}]${name || createName()}`;
createName.asChild                                                      = (parent: string, child: string) => `{${parent}} ${child}`;
createName.asComponent                                                  = (parent: string, component: string) => `${parent}*${component}`;
export const configureName: configurationHandler | { ofType: () => {} } = (name, owner) => owner[SM_ID] = createName(name);
configureName.ofType = (type): configurationHandler => {
    return (name, owner) => configureName(createName.ofType(type, name), owner);
};

