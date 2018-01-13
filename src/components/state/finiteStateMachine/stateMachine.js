// @flow

import State from './state';
import {fsmStateName, fsmStateDefinition} from './types'
import {InvalidStateError} from "./errors"
import {GET_STATE} from "./internal";

/**
 * This represents the potential stages that the owner of this FiniteStateMachine can be in
 */
class StateMachine {
    _states: { [state: fsmStateName]: State };
    _currentState: State;
    _defaultState: State;
    
    constructor() {
        this._states = {};
    }
    
    get currentState(): State {
        return this._currentState || this._defaultState;
    }
    
    /**
     * Transition from one state to the next
     *
     * @param {fsmStateName} nextStateName
     * @return {StateMachine}
     */
    transition(nextStateName: fsmStateName) {
        this._currentState = this.currentState.transition(nextStateName, this);
        return this;
    }
    
    /**
     * Add a State to the StateMachine
     * @param stateDefinition
     */
    addState(stateDefinition: fsmStateDefinition) {
        const fsmState = this._states[stateDefinition.name] = new State(stateDefinition.name);
        
        fsmState.setPotentialNextStates(stateDefinition.possibleNextStates);
        
        // if this is the first step that we're adding (and we haven't set a state yet) set the default State to be the current one
        if (!this._defaultState) this.setDefaultState(fsmState);
        
        return this;
    }
    
    setDefaultState(state) {
        this._defaultState = state;
        return this;
    }
    
    [GET_STATE](stateName: fsmStateName): State {
        const nextState = this._states[stateName];
        if (!nextState) throw new InvalidStateError('Could not find state matching ' + stateName);
        return nextState;
    }
}

export default StateMachine;