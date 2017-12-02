/**
 * Created by Sam Washington on 5/20/17.
 */
import {describe, it} from "mocha";
/** @alias {Sm}  */
import {Sm} from "../../../Sm"

import {expect} from "chai";

describe('_config', () => {
    it('can configure something', () => {
        const PHP_Application = (Sm._config.PHP_Application);
        const app             = new PHP_Application();
        return app.configure({
                                 models: {
                                     test: {
                                         properties: {
                                             id: {primary: true}
                                         }
                                     }
                                 }
                             })
                  .then(result => {
                      console.log(result);
                  });
    });
});