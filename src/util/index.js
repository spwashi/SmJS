export const arraysEqual = (a: Array, b: Array) => {
    if (a === b) return true;
    if (a == null || b == null) return false;
    if (a.length !== b.length) return false;
    
    // If you don't care about the order of the elements inside
    // the array, you should sort both arrays here.
    
    for (let i = 0; i < a.length; ++i) {
        if (a[i] !== b[i]) return false;
    }
    return true;
};