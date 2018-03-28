export const SM_ID              = '** SM ID **';
export const createName         = (name) => name || Math.random().toString(36).substr(4, 6);
createName.asInstance           = (parent: string, child: string) => `<${parent}> ${child}`;
createName.ofType               = (parentName, childName) => {
    childName             = createName(childName);
    const isAlreadyOfType = createName.isOfType(parentName, childName);
    
    if (isAlreadyOfType) return childName;
    
    const type = `[${parentName}]`;
    return `${type}${childName}`;
};
createName.isOfType             = (parentName, childName) => {
    const type = `[${parentName}]`;
    return childName.substr(0, type.length) === type;
};
createName.asComponent          = (parent: string, component: string) => `{${parent}} ${component}`;
createName.asIdentifiedInstance = (parent: string, instance: string) => createName.asInstance(parent, `#${instance}`)
