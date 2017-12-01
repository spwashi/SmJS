/// <reference path="/docs/global.d.ts" />
namespace Sm {
    namespace entities {
        /**
         * @extends Sm.std.Std
         */
        class ConfiguredEntity extends Sm.std.Std {
            constructor(name, config: {});

            static init(): Promise<ConfiguredEntity>;

            _parentSymbols: Set<any>;

            get parentSymbols(): Set<Symbol>;

            get jsonFields(): Array<string>;

            get configuration(): ConfiguredEntity.Configuration;

            initialize(config): Promise<ConfiguredEntity>;

            configure(config: ConfiguredEntity._config): Promise<ConfiguredEntity.Configuration>;
        }

        namespace ConfiguredEntity {
            interface _config {
                _id?: string
            }

            class Configuration {
                static create(config: object);

                get current(): {};
            }
        }
    }
}