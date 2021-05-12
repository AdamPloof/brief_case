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
            chartLabels: [],
            chartData: [],
            category: 'All',
            loading: true,
        }
        this.chart = null;
    }

    componentDidMount() {
        this.fetchStats();
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.data != prevState.data) {
            this.setChartData();
        }

        if (this.state.chartData != prevState.chartData && this.state.chartData.length != 0) {
            this.updateChart();
        }
    }

    setChartData() {
        let chartLabels = this.state.data.map(stats => {
            return stats.EndOfWeekDate;
        });

        let chartData = this.state.data.map(stats => {
            return stats[this.state.category];
        });
        
        this.setState({
            chartLabels,
            chartData,
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

    drawChart() {
        const labels = [...this.state.chartLabels];
        const data = {
            labels: labels,
            datasets: [{
                label: 'Incidents by Week',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: [...this.state.chartData],
            }]
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