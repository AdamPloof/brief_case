import React, { Component } from 'react';
import CaseSearchModal from './components/CaseSearchModal';
import Routing from './routing';

class CaseSearch extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedInput: null,
            cases: [],
        };
        this.changeSelectedInput = this.changeSelectedInput.bind(this);
    }

    componentDidMount() {
        this.getCases();
    }

    getCases() {
        let url = Routing.generate('fetchcases');
        const params = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        };
        fetch(url, params).then(res => res.json())
            .then(data => {
                this.setState({
                    cases: [...this.state.cases, ...data]
                });
            })
            .catch(err => {
                console.log(err);
            })
    }

    changeSelectedInput() {
        return;
    }

    render() { 
        return (
            <CaseSearchModal 
                cases={this.state.cases}
                changeSelectedInput={this.changeSelectedInput}
            />
        );
    }
}
 
export default CaseSearch;
