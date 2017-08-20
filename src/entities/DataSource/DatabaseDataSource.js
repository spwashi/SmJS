import DataSource from "./DataSource";

class DatabaseDataSource extends DataSource {
    static get type() {
        return 'database';
    }
    
    boonman() {
        return 'grandslam';
    }
}

export default DatabaseDataSource;
export {DatabaseDataSource}