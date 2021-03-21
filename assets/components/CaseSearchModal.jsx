import React, { Component } from 'react';
import $ from 'jquery';

import CasesTable from './CasesTable';

class CaseSearchModal extends Component {
    render() { 
        return (
            <div className="modal fade" id="caseSearchModal" tabIndex="-1" aria-labelledby="caseSearchModalLabel" aria-hidden="true">
                <div className="modal-dialog" style={{maxWidth: "750px"}}>
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="caseSearchModalLabel">Select Related Case</h5>
                            <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div className="modal-body">
                            <CasesTable 
                                cases={this.props.cases}
                                selectCase={this.props.selectCase}
                            />
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" className="btn btn-primary">Select Case</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
 
export default CaseSearchModal;
