import DataSource from "./DataSource";
import TableDataSource from "./TableDataSource";
import DatabaseDataSource from "./DatabaseDataSource";

export {DataSource, TableDataSource, DatabaseDataSource};
DataSource.registerType(TableDataSource);
DataSource.registerType(DatabaseDataSource);