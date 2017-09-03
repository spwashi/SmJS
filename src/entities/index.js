/** @name {Sm.entities}  */
import Model from "./Model/Model";
import Property from "./Property/Property";
import EntityType from "./EntityType/EntityType";
import Datatype from "./Datatype";
import ConfiguredEntity from "./ConfiguredEntity";
import {DatabaseDataSource, DataSource, TableDataSource} from "./DataSource";
// configuration for the frameworkentities

export const entities = {
    ConfiguredEntity,
    Property,
    EntityType,
    Model,
    Datatype,
    TableDataSource,
    DatabaseDataSource,
    DataSource
};
export default entities;