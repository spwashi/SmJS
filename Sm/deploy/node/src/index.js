import entities from "./entities/";
import Property from "./classes/Property";

console.log('\n');

let std_properties = entities.models._.properties;
let Properties     = {};

for (let property_name in std_properties) {
    if (!std_properties.hasOwnProperty(property_name)) continue;

    let _Property             = new Property(property_name,
                                             std_properties[property_name]);
    Properties[property_name] = (_Property)
}



