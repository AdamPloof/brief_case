import React, { Component } from 'react';
import Chart from 'chart.js/auto';
import Routing from '../../routing';

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
            chartReady: false,
            loading: true,
        }
        this.chart = null;
    }

    componentDidMount() {
        this.fetchStats();
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.props.categoryColorMap != prevProps.categoryColorMap) {
            this.setChartReady();
        }

        if (this.state.data != prevState.data) {
            this.setChartReady();
        }

        if (this.state.chartReady != prevState.chartReady && this.state.chartReady == true) {
            this.setChartData();
        }

        if (this.state.datasets != prevState.datasets) {
            this.updateChart();
        }
    }

    setChartReady() {
        if (this.props.categoryColorMap !== null && this.state.data.length != 0) {
            this.setState({
                chartReady: true,
            });
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

        const color = category == 'All' ? 'rgb(245, 111, 66)' : this.props.categoryColorMap.get(category);
        const hidden = category != 'All';
        // 245, 111, 66

        return {
            label: category,
            backgroundColor: color,
            borderColor: color,
            data,
            hidden,
        };
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
            options: {
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'start',
                    }
                },
            }
        };
        
        const ctx = document.getElementById('casesOverTime');
        this.chart = new Chart(ctx, config);
    }

    render() { 
        return (
            <div className="report-box">
                <div className="box-header">
                    <h1>Cases by Week</h1>
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