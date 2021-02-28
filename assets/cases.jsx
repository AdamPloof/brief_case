import { isRuntimeEnvironmentConfigured } from '@symfony/webpack-encore';
import React, { Component } from 'react';
import ReactDom from 'react-dom';

class CasesTable extends Component {
    render() { 
        return (
            <h1>This is the Index</h1>
        );
    }
}
 
ReactDom.render(<CasesTable />, document.getElementById("cases-table"));
