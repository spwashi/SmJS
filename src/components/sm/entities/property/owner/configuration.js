import {PropertyOwner} from "./index";
import PropertyConfig from "../configuration";
import {Property} from "../property";

export const configurePropertyForPropertyOwner =
                 (originalPropertyName: string, originalPropertyConfig: { name: string }, propertyOwner: PropertyOwner) => {
                     originalPropertyConfig.name = propertyOwner.createPropertyName_Identity(originalPropertyName);
                     const propertyConfig        = new PropertyConfig(originalPropertyConfig);
                     const addPropertyToEntity   =
                               property => {
                
                                   propertyOwner.properties[originalPropertyName] = property;
                
                                   const propertyMeta = propertyOwner.propertyMeta;
                                   propertyMeta.incorporateProperty(property);
                                   return property;
                               };
        
                     return propertyConfig.configure(new Property)
                                          .then(addPropertyToEntity);
                 };
