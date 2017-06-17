import {GenericError} from "./Error";

export default class TimeoutError extends GenericError {
    constructor(message, symbol, granted_time, unit) {
        message = message || 'Timeout ';
        unit    = unit || 'ms';
        if (granted_time && typeof message === 'string') {
            message += ` (${granted_time} ${unit})`;
        }
        super(message, symbol);
    }
}