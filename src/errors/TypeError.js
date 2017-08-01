import {GenericError} from "./Error";

export default class TypeError extends GenericError {
    constructor(message, symbol) {
        message = message || 'Incorrect type ';
        super(message, symbol);
    }
}