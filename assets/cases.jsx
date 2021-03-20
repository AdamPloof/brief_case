import React, { Component } from 'react';
import ReactDom from 'react-dom';
import CasesPager from './components/CasesPager';

// Twig Routing
// Dump routes: bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);

class CasesTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cases: [],
            page: 1,
            pages: 1,
            casesPerPage: 10,
        };
        this.changePage = this.changePage.bind(this);
    }

    componentDidMount() {
        this.getCases();
    }

    componentDidUpdate(prevProps, prevState) {
        let pages = 1;
        if (this.state.cases != prevState.cases) {
            pages = this.state.cases.length / this.state.casesPerPage;
            // Checking if whole number, if not adding one page for remainder
            if (pages % 1 != 0) {
                pages = Math.ceil(pages);      
            }
            this.setState({
                pages
            });
        }
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

    buildCasesTable() {
        const pageStart = (this.state.page - 1) * this.state.casesPerPage;
        const pageEnd = this.state.page * this.state.casesPerPage;
        const casesPage = this.state.cases.slice(pageStart, pageEnd);
        let table = (
            <table className="table table-striped table-bordered table-hover">
                <thead className="thead-dark">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Description</th>
                        <th scope="col">Category</th>
                        <th scope="col">Primary Person</th>
                        <th scope="col">Video</th>
                    </tr>
                </thead>
                <tbody>
                    {casesPage.map((caseFile) => this.makeCaseRow(caseFile))}
                </tbody>
            </table>
        );
        return table;
    }

    makeCaseRow(caseFile) {
        let caseUrl = Routing.generate('viewcase', {id: caseFile.id});
        let caseDate = caseFile.date.split('T')[0];
        return (
            <tr key={caseFile.id}>
                <th scope="row">{caseDate}</th>
                <td><a href={caseUrl}>{caseFile.description}</a></td>
                <td>{caseFile.category}</td>
                <td>{caseFile.primaryPerson.name}</td>
                <td>{caseFile.video ? "Yes" : "No"}</td>
            </tr>
        );
    }

    changePage(page) {
        this.setState({
            page
        });
    }

    render() { 
        return (
            <div className="cases-table">
                {this.buildCasesTable()}
                <CasesPager 
                    pages={this.state.pages}
                    page={this.state.page}
                    changePage={this.changePage}
                />
            </div>
            
        );
    }
}
 
ReactDom.render(<CasesTable />, document.getElementById("index-container"));
