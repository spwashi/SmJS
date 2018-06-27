import PropertyConfig, {handlers}       from "../../property/configuration";
import {EntityProperty}                 from "./property";
import Identity                         from "../../../../identity/components/identity";
import {Sm}                             from "../../sm";
import {parseSmID}                      from "../../../utility";
import {SmEntity}                       from "../../smEntity";
import PropertyAsReferenceConfiguration from "../../property/reference/configuration";
import {PropertyAsReferenceDescriptor}  from "../../property/reference";
import {Configuration}                  from "../../../../configuration/configuration";
import type {ConfigurationSession}      from "../../../../configuration/types";

export class EntityPropertyConfig extends PropertyConfig {
	handlers = {
		derivedFrom: (identity: Identity, entityProperty: EntityProperty, configuration: ConfigurationSession & Configuration) => {
			if (!identity) return null;
			if (!identity.identifier && identity.identity) {
				return configuration.waitFor('name')
				                    .then(name => Sm.getManagerForSmID(identity.identity || parseSmID(`${name}`).owner))
				                    .then((Manager: SmEntity) => {
					                          const refConfig         = new PropertyAsReferenceConfiguration(identity, configuration);
					                          refConfig.smEntityProto = Manager;

					                          let referenceDescriptor = new PropertyAsReferenceDescriptor;
					                          let referenceConfigured = refConfig.configure(referenceDescriptor);

					                          return referenceConfigured.then(descriptor => entityProperty._derivedFrom = descriptor.hydrationMethod);
				                          }
				                    );
			}

			try {
				const {owner, name} = parseSmID(identity.identifier);
				return Sm.init(owner)
				         .then(item => {
					         item.properties[name]._reference && (entityProperty._reference = entityProperty._reference || item.properties[name]._reference);
					         item.properties[name]._datatypes && (entityProperty._datatypes = entityProperty._datatypes || item.properties[name]._datatypes);
					         item.properties[name]._default && (entityProperty._default = entityProperty._default || item.properties[name]._default);
					         item.properties[name]._length && (entityProperty._length = entityProperty._length || item.properties[name]._length);
					         return entityProperty._derivedFrom = identity;
				         });

			} catch (e) {
				return entityProperty._derivedFrom = identity;
			}

		},

		...handlers,

		role:      (role, entityProperty: EntityProperty) => {
			if (!role) return null;
			if (role !== 'value') throw new Error("The only supported EntityProperty role at the moment is 'value'");
			return entityProperty._role = role;
		},
		minLength: (minLength, entityProperty: EntityProperty) => {
			if (!minLength) return null;

			if (typeof minLength !== 'number') throw new Error("Can only use numbers to config min lengths");

			return entityProperty._minLength = minLength;
		},
		contexts:  (contexts, entityProperty: EntityProperty) => {
			if (!contexts) return [];
			if (!Array.isArray(contexts)) {
				throw new Error("Unsure of what to do with non-array contexts");
			}

			contexts.forEach(context => {
				if (typeof context !== 'string') {
					throw new Error("Unsure of what to do with non-string contexts");
				}
			});

			return entityProperty._contexts = contexts;
		}
	}
}