import React, { Component } from 'react';
import ReactDom from 'react-dom';
import CasesTable from './components/CasesTable';
import Routing from './routing';

class CasesIndex extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cases: [],
        };
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

    render() {
        return (
            <CasesTable cases={this.state.cases} />
        );
    }
}
 
ReactDom.render(<CasesIndex />, document.getElementById("index-container"));
