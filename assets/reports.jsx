import React, { Component } from 'react';
import ReactDom from 'react-dom';
import Chart from 'chart.js/auto';

import CasesOverTime from './components/reports/CasesOverTime';

class CaseReports extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cases: null
        }
    }
    
    render() { 
        return (
            <div className="report-wrapper">
                <CasesOverTime cases={this.state.cases} />
                <div className="report-box">

                </div>

            </div>
        );
    }
}
 
ReactDom.render(<CaseReports />, document.getElementById('report-container'));
