export const parseSmID            = smID => {
    if (typeof smID === "object" && smID) smID = smID.smID;
    
    if (!smID) throw new Error("Cannot resolve SmID from provided argument");
    
    let managerRegexp          = '^\\[(?<manager>[a-zA-Z_]+)]';
    let ownerRegexp            = '(?:\\{(?<owner>\\[[a-zA-Z_]+][a-zA-Z_]+)})';
    let nameRegexp             = '(?<name>[a-zA-Z_]+)';
    let regex                  = XRegExp(`${managerRegexp}${ownerRegexp}?\\s?${nameRegexp}`, 'g');
    let match                  = XRegExp.exec(smID, regex);
    let {manager, owner, name} = match || {};
    let parsed                 = {};
    
    Object.entries({manager, owner, name})
          .forEach(entry => {
              let [i, v] = entry;
              if (v) parsed[i] = v;
          });
    
    return parsed;
};