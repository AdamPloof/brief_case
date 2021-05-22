import React, { Component } from 'react';
import ReactDom from 'react-dom';
import Routing from './routing';

import CasesOverTime from './components/reports/CasesOverTime';
import CasesByCategory from './components/reports/CasesByCategory';
import CasesByDayOfWeek from './components/reports/CasesByDayOfWeek';

import { chartColors } from './components/utils/colors';

class CaseReports extends Component {
    constructor(props) {
        super(props);

        this.state = {
            categoryData: [],
            categoryColorMap: null,
            loading: true,
        };

        this.chartColors = [...chartColors];
        this.colorsInUse = [];

        this.setCategoryColors = this.setCategoryColors.bind(this);
    }

    componentDidMount() {
        this.fetchStats();
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.categoryData != prevState.categoryData) {
            this.setCategoryColors();
        }
    }

    fetchStats() {
        const url = Routing.generate('cases_by_category');
        const params = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        }

        fetch(url, params).then(res => res.json())
            .then(data => {
                this.setState({
                    categoryData: data,
                    loading: false,
                })
            })
            .catch(err => {
                console.log(err);
            });
    }

    setCategoryColors() {
        const categoryColorMap = new Map();

        for (let category of [...this.state.categoryData]) {
            categoryColorMap.set(category.category, this.getRandomChartColor());
        }

        this.setState({
            categoryColorMap,
        });
    }

    getRandomChartColor() {
        // Recycle colors in use back into available colors once all colors have been used
        // Rinse and repeat as needed.
        if (this.chartColors.length == 0) {
            this.chartColors = [...this.colorsInUse];
            this.colorsInUse = [];
        }

        let color = this.chartColors.pop();
        this.colorsInUse.push(color);

        return color;
    }

    render() { 
        return (
            <div className="report-wrapper">
                <CasesOverTime
                    categoryColorMap={this.state.categoryColorMap}
                />
                <CasesByCategory 
                    data={this.state.categoryData}
                    categoryColorMap={this.state.categoryColorMap}
                />
                <CasesByDayOfWeek />
            </div>
        );
    }
}
 
ReactDom.render(<CaseReports />, document.getElementById('report-container'));
