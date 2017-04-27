import entities from "./_config/";
import Property from "./src/Property";

console.log('\n');

let Properties = {};

((Properties) => {
    let std_properties = entities.models._.properties;

    let models = entities.models;
    for (let _model in models) {
        if (!models.hasOwnProperty(_model))



            }

    for (let property_name in std_properties) {
        if (!std_properties.hasOwnProperty(property_name)) continue;

        let _Property             = new Property(property_name, std_properties[property_name]);
        Properties[property_name] = (_Property)
    }

    try {
        throw {test: 'hello'};
    } catch (e) {
        console.log(e);
    }

})(Properties);

console.log(Properties);