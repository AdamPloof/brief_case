import React, { Component } from 'react';
import Chart from 'chart.js/auto';

class CasesOverTime extends Component {
    constructor(props) {
        super(props);
        this.state = {
            startDate: null,
            endDate: null
        }
        this.chart = null;
    }

    componentDidMount() {
        this.makeLineChart();
    }

    makeLineChart() {
        const labels = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
        ];
        const data = {
            labels: labels,
            datasets: [{
                label: 'My First dataset',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: [0, 10, 5, 2, 20, 30, 45],
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