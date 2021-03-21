import React, { Component } from 'react';
import Routing from '../routing';

import CasesPager from './CasesPager';

class CasesTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            page: 1,
            pages: 1,
            casesPerPage: 10,
        };
        this.changePage = this.changePage.bind(this);
    }

    componentDidUpdate(prevProps, prevState) {
        let pages = 1;
        if (this.props.cases != prevProps.cases) {
            pages = this.props.cases.length / this.state.casesPerPage;
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
        const pageStart = (this.state.page - 1) * this.state.casesPerPage;
        const pageEnd = this.state.page * this.state.casesPerPage;
        const casesPage = this.props.cases.slice(pageStart, pageEnd);
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
                <td>
                    <a 
                        href={caseUrl}
                        onClick={(e) => {
                            if (typeof this.props.selectCase != 'undefined') {
                                this.props.selectCase(e, caseFile.id);
                            }
                        }}
                    >
                        {caseFile.description}
                    </a>
                </td>
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
 
export default CasesTable;