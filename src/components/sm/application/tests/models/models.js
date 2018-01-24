const DATETIME_ = 'datetime';
const STRING_   = 'string';
const INTEGER_  = 'int';
const NULL_     = 'null';

export const models = {
    _:       {
        properties: {
            id:             {
                primary:     !0,
                datatypes:   INTEGER_,
                length:      11,
                isGenerated: true
            },
            delete_dt:      {
                datatypes: DATETIME_,
            },
            creation_dt:    {
                datatypes:    DATETIME_,
                defaultValue: 'now'
            },
            last_update_dt: {
                datatypes:   DATETIME_,
                updateValue: 'now'
            },
        }
    },
    users:   {
        inherits: '_',
        
        properties: {
            email:      {datatypes: [STRING_]},
            first_name: {datatypes: [STRING_, NULL_]},
            last_name:  {datatypes: [STRING_, NULL_]}
        }
    },
    courses: {
        inherits:   '_',
        properties: {
            department:    {},
            title:         {
                datatypes: STRING_,
                length:    25,
                unique:    true
            },
            course_number: {
                unique:    true,
                datatypes: [INTEGER_, NULL_],
                length:    11
            }
        }
    }
};

export default models;