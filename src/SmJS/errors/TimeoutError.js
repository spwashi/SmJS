import {GenericError} from "./Error";

export default class TimeoutError extends GenericError {
    constructor(message, symbol, granted_time, unit) {
        message = message || 'Timeout ';
        unit    = unit || 'milliseconds';
        if (granted_time && typeof message === 'string') {
            message += ` (timeout after ${granted_time} ${unit})`;
        }
        super(message, symbol);
    }
}