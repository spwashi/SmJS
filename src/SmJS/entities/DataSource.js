import ConfiguredEntity from "./ConfiguredEntity";

export default class DataSource extends ConfiguredEntity {
    static get name() {return 'DataSource'; }
    
    constructor(name, config) {
        super(name, config);
        this.complete(DataSource.name);
    }
    
}