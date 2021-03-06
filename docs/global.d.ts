interface promiseEssence {
    reject: null | Function,
    resolve: null | Function
}

declare namespace Sm {
    type smID = string;

    namespace std {
        class Std {
            static smID: smID;
                   smID: Sm.smID;

            static init(): Promise<Std>;
        }
    }
    namespace entities {
        class Property {
        }

        class Model {
        }

        class Datatype {
        }

        class TableDataSource {
        }

        class DatabaseDataSource {
        }
    }
}