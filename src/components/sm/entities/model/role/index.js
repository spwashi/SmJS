export class ModelRole {
    roleName;
    propertyNames;
    
    constructor(roleName, properties) {
        this.roleName   = roleName;
        this.properties = properties;
    }
    
    toJSON() {
        return {
            roleName:      this.roleName,
            propertyNames: this.propertyNames
        }
    }
}

export type mappedModelRoleObject = { modelRole: ModelRole };