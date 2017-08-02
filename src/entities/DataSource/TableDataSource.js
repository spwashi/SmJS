import DataSource from "./DataSource";

/**
 * @extends DataSource
 */
class TableDataSource extends DataSource {
    static get type() { return 'table';}
}

export default TableDataSource;
export {TableDataSource}