export const SM_ID = Symbol('PROPERTY NAME');

export const configureName = (name, owner) => {
    owner[SM_ID] = name || Math.random().toString(36).substr(4, 6);
};

configureName.ofType = (type) => {
    return (name, owner) => configureName(`[${type}]${name}`, owner);
};