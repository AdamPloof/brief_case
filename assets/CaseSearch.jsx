import React, { Component } from 'react';
import CaseSearchModal from './components/CaseSearchModal';
import Routing from './routing';
import $ from 'jquery';

class CaseSearch extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cases: [],
        };
        this.selectCase = this.selectCase.bind(this);
    }

    componentDidMount() {
        this.getCases();
    }

    getCases() {
        // TODO: When editing a case, should exclude self from case list
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

    selectCase(e, caseId) {
        // Set the value of the selected related case inputs to the selected case from CaseSearchModal
        e.preventDefault();

        // Example input: case_related_cases_0_description
        let selectedParts = document.getElementById('caseSearchModal').dataset.selected.split('_');
        selectedParts.pop();
        let selectedStr = selectedParts.join('_');

        const descriptionInput = document.getElementById(selectedStr + '_description');
        const idInput = document.getElementById(selectedStr + '_case_id');

        let selectedCase = this.state.cases.find(caseFile => caseFile.id == caseId);
        descriptionInput.value = selectedCase.description;
        idInput.value = selectedCase.id;

        // Close the modal
        $('#caseSearchModal').modal('hide');
    }

    render() { 
        return (
            <CaseSearchModal 
                cases={this.state.cases}
                selectCase={this.selectCase}
            />
        );
    }
}
 
export default CaseSearch;
