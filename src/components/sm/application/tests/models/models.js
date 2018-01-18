const DATETIME_ = 'datetime';
const STRING_   = 'string';
const INTEGER_  = 'int';
const NULL_     = 'null';

export const models = {
    _:            {
        properties: {
            id:             {
                primary:   !0,
                datatypes: INTEGER_,
                length:    11,
            },
            delete_dt:      {
                datatypes: DATETIME_,
            },
            creation_dt:    {
                datatypes: DATETIME_,
                _default:  'now'
            },
            last_update_dt: {
                datatypes: DATETIME_
            },
        }
    },
    universities: {
        inherits: '_',
        
        properties: {}
    },
    courses:      {
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