import {describe, it} from "mocha";
import {expect} from 'chai'
import StateMachine from "../stateMachine";
import {InvalidStateError} from "../errors";

describe('StateMachine', () => {
    const createStateMachine = () => {
        const stateMachine = new StateMachine;
        const states       = [
            {
                name:               'BEGIN',
                possibleNextStates: ['CONTINUE', 'END']
            },
            {
                name:               'PAUSE',
                possibleNextStates: ['CONTINUE', 'END']
            },
            {
                name:               'CONTINUE',
                possibleNextStates: ['END', 'PAUSE']
            },
            {
                name:               'END',
                possibleNextStates: ['BEGIN']
            }
        ];
        states.forEach(def => stateMachine.addState(def));
        return stateMachine;
    };
    
    it('Can Transition between States', () => {
        const stateMachine = createStateMachine();
        
        stateMachine.transition('CONTINUE');
        
        let currentState = stateMachine.currentState;
        expect(currentState.name).to.equal('CONTINUE');
        
    });
    it('Cannot transition to improper state', () => {
        // is in a state that we haven't specified that it's allowed to transition to
        //    in this case, starting on the "BEGIN" state means that we can either 'CONTINUE' or 'END';
        //    not 'BEGIN' as we attempt to
        const stateMachine = createStateMachine();
        expect(() => stateMachine.transition('BEGIN')).to.throw(InvalidStateError);
    });
});