import ConfiguredEntity from "./ConfiguredEntity";

export default class Source extends ConfiguredEntity {
    static get name() {return 'Source'; }
    
    constructor(name, config) {
        super(name, config);
        this.complete(Source.name);
    }
    
}