import React, { Component } from 'react';
import ReactDom from 'react-dom';
import PersonsTable from './components/PersonsTable';

import Routing from './routing';

class PersonsList extends Component {
    constructor(props) {
        super(props);
        this.state = {
            persons: []
        }
    }

    componentDidMount() {
        this.getPersonss();
    }

    getPersonss() {
        let url = Routing.generate('fetchpersons');
        const params = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        };
        fetch(url, params).then(res => res.json())
            .then(data => {
                this.setState({
                    persons: [...this.state.persons, ...data]
                });
            })
            .catch(err => {
                console.log(err);
            })
    }

    render() {
        return (
            <PersonsTable persons={this.state.persons} />
        );
    }
}
 
ReactDom.render(<PersonsList />, document.getElementById('persons-list-container'));
