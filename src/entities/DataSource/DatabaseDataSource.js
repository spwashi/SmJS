import DataSource from "./DataSource";

class DatabaseDataSource extends DataSource {
    static get type() { return 'database';}
}

export default DatabaseDataSource;
export {DatabaseDataSource}