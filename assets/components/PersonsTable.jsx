import React, { Component } from 'react';
import CasesPager from './CasesPager';

import Routing from '../routing';

class PersonsTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            page: 1,
            pages: 1,
            personsPerPage: 10,
        };
        this.changePage = this.changePage.bind(this);
    }

    componentDidUpdate(prevProps, prevState) {
        let pages = 1;
        if (this.props.persons != prevProps.persons) {
            pages = this.props.persons.length / this.state.personsPerPage;
            // Checking if whole number, if not adding one page for remainder
            if (pages % 1 != 0) {
                pages = Math.ceil(pages);      
            }
            this.setState({
                pages
            });
        }
    }

    buildCasesTable() {
        const pageStart = (this.state.page - 1) * this.state.personsPerPage;
        const pageEnd = this.state.page * this.state.personsPerPage;
        const personsPage = this.props.persons.slice(pageStart, pageEnd);
        let table = (
            <table className="table table-striped table-bordered table-hover">
                <thead className="thead-dark">
                    <tr>
                        <th scope="col">Person</th>
                        <th scope="col">Cases - Primary</th>
                        <th scope="col">Cases - Associated</th>
                    </tr>
                </thead>
                <tbody>
                    {personsPage.map((person) => this.makeCaseRow(person))}
                </tbody>
            </table>
        );
        return table;
    }

    makeCaseRow(person) {
        let personUrl = Routing.generate('viewperson', {name: person.name});
        return (
            <tr key={person.name}>
                <td>
                    <a href={personUrl}>
                        {person.name}
                    </a>
                </td>
                <td>{person.primary.length}</td>
                <td>{person.associated.length}</td>
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
 
export default PersonsTable;
