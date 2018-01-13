// @flow

/**
 * Represents a discrete State in a FiniteStateMachine
 */
import {fsmStateName, fsmStepTransitionFn} from "./types";
import {InvalidStateError} from "./errors";
import {GET_STATE} from "./internal";

/**
 * An object from which we an retrieve a State by name
 */
export type fsmStateManager = {
    /**
     * Retrieve the state (going by a specific name) from the StateManager
     */
    [GET_STATE]: (nextStateName: fsmStateName) => State
}

class State {
    // where can we go from here?
    _potentialNextStates: [fsmStateName];
    _name: string;
    
    constructor(name) {
        this._name = name;
    }
    
    get name() {
        return this._name;
    }
    
    setPotentialNextStates(value: [fsmStateName]) {
        this._potentialNextStates = value;
        return this;
    }
    
    transition(nextStateName: fsmStateName, stateManager: fsmStateManager) {
        if (this._potentialNextStates.indexOf(nextStateName) < 0) {
            throw new InvalidStateError(`Cannot transition to ${nextStateName}`);
        }
        
        return stateManager[GET_STATE](nextStateName);
    }
}

export default State;