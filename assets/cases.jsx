import React, { Component } from 'react';
import ReactDom from 'react-dom';

// Twig Routing
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);

class CasesTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cases: [],
            page: 1, 
        }
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
            <h1>This is the Index</h1>
        );
    }
}
 
ReactDom.render(<CasesTable />, document.getElementById("cases-table"));
