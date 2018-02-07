export const SM_ID      = Symbol('PROPERTY NAME');
export const createName = (name) => name || Math.random().toString(36).substr(4, 6);
createName.asInstance   = (parent: string, child: string) => `${parent}${child}`;
createName.ofType       = (type, name) => createName.asInstance(`[${type}]`, name);
createName.asComponent  = (parent: string, component: string) => `{${parent}}${component}`;
