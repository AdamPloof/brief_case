import React, { Component } from 'react';
import ReactDom from 'react-dom';

import CasesOverTime from './components/reports/CasesOverTime';
import CasesByCategory from './components/reports/CasesByCategory';
import CasesByDayOfWeek from './components/reports/CasesByDayOfWeek';

// TODO: Centralize the color coding of categories here pass as props to individual reports/charts

class CaseReports extends Component {

    render() { 
        return (
            <div className="report-wrapper">
                <CasesOverTime />
                <CasesByCategory />
                <CasesByDayOfWeek />
            </div>
        );
    }
}
 
ReactDom.render(<CaseReports />, document.getElementById('report-container'));
