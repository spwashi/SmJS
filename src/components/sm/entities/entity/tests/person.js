export default {
    component: {
        userIdentity:     {
            model: '[Model]users',
            finds: {
                email_address: {
                    container: 'email_addresses',
                    source:    '[Model]email_addresses'
                }
            }
        },
        employeeIdentity: {
            model:   '[Model]employees',
            context: [
                {company: '[Model]companies'}
            ],
            
            finds: {
                email_address: {
                    container: 'email_addresses'
                }
            }
        }
    },
    
    properties: {
        first_name:      {
            index:       true,
            derivedFrom: 'userIdentity',
            datatypes:   ['string'],
        },
        last_name:       {
            index:       true,
            derivedFrom: 'userIdentity',
            datatypes:   ['string'],
        },
        email_addresses: {
            propertyType: 'container',
            derivedFrom:  {
                userIdentity:     {
                    property: 'email_address',
                    
                },
                employeeIdentity: {
                    property: 'email_address'
                }
                
            }
        },
        username:        {
            index:       true,
            derivedFrom: 'userIdentity',
            datatypes:   ['string'],
        },
    }
}