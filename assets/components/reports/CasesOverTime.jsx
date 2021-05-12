import React, { Component } from 'react';
import Chart from 'chart.js/auto';
import Routing from '../../routing';
import { chartColors } from '../utils/colors';

class CasesOverTime extends Component {
    constructor(props) {
        super(props);
        this.state = {
            startDate: null,
            endDate: null,
            data: [],
            labels: [],
            datasets: [],
            categories: [],
            category: 'All',
            loading: true,
        }
        this.chart = null;
        this.chartColors = [...chartColors];
        this.colorsInUse = [];
    }

    componentDidMount() {
        this.fetchStats();
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.data != prevState.data) {
            this.setChartData();
        }

        if (this.state.datasets != prevState.datasets && this.state.datasets.length != 0) {
            this.updateChart();
        }
    }

    setChartData() {
        const labels = this.state.data.map(stats => {
            return stats.EndOfWeekDate;
        });

        let categories = Object.keys(this.state.data[0]);
        const idx = categories.indexOf('EndOfWeekDate');
        categories.splice(idx, 1);

        const datasets = this.makeDatasets(categories);
        
        this.setState({
            labels,
            datasets,
            categories,
        });
    }

    updateChart() {
        if (!this.chart) {
            this.drawChart();
            return;
        }

        this.chart.update('reset');
    }

    fetchStats() {
        const url = Routing.generate('cases_over_time');
        const params = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        }

        fetch(url, params).then(res => res.json())
            .then(data => {
                this.setState({
                    data,
                    loading: false,
                })
            })
            .catch(err => {
                console.log(err);
            });
    }

    makeDatasets(categories) {
        let datasets = [];
        for (let category of categories) {
            datasets.push(this.makeDataset(category));
        }

        return datasets;
    }

    makeDataset(category) {
        const data = this.state.data.map(stats => {
            return stats[category];
        });

        const color = this.getRandomChartColor();

        return {
            label: category,
            backgroundColor: color,
            borderColor: color,
            data
        };
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

    drawChart() {
        const labels = [...this.state.labels];
        const data = {
            labels: labels,
            datasets: [...this.state.datasets]
          };

        const config = {
            type: 'line',
            data,
            options: {}
        };
        
        const ctx = document.getElementById('casesOverTime');
        this.chart = new Chart(ctx, config);
    }

    render() { 
        return (
            <div className="report-box">
                <div className="box-header">
                    <h1>Cases over time</h1>
                </div>
                <div className="box-body">
                    <div className="chart-container">
                        <canvas id="casesOverTime"></canvas>
                    </div>
                </div>
            </div>
        );
    }
}
 
export default CasesOverTime;