import React, { Component } from 'react';
import Chart from 'chart.js/auto';
import Routing from '../../routing';
import { chartColors } from '../utils/colors';

class CasesByDayOfWeek extends Component {
    constructor(props) {
        super(props);
        this.state = {
            data: [],
            labels: [],
            chartData: [],
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

        if (this.state.chartData != prevState.chartData && this.state.chartData.length != 0) {
            this.updateChart();
        }
    }

    setChartData() {
        const labels = this.state.data.map(stats => {
            return stats.day;
        });
        const chartData = this.state.data.map(stats => {
            return stats.caseCount;
        });
        
        this.setState({
            labels,
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
        const url = Routing.generate('cases_by_day');
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

    makeDataset() {
        const data = [...this.state.chartData];
        let borderColors = [];

        for (let i = 0; i < data.length; i++) {
            borderColors.push(this.getRandomChartColor());
        }

        let backgroundColors = this.makeColorsTransparent(borderColors);

        return {
            label: 'Cases by Day of Week',
            backgroundColor: backgroundColors,
            borderColor: borderColors,
            data,
            borderWidth: 1
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

    makeColorsTransparent(colors) {
        let transparentColors = [];
        for (let color of colors) {
            transparentColors.push(this.addOpacityToColor(color));
        }

        return transparentColors;
    }

    addOpacityToColor(color) {
        return color.slice(0, -1) + ', 0.2)';
    }

    drawChart() {
        const labels = [...this.state.labels];
        const data = {
            labels: labels,
            datasets: [this.makeDataset()]
          };

        const config = {
            type: 'bar',
            data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                },
            }
        };
        
        const ctx = document.getElementById('casesByDay');
        this.chart = new Chart(ctx, config);
    }

    getCanvasContainerStyle() {
        return {
            display: "block",
            boxSizing: "border-box",
            width: "768px",
        };
    }

    render() { 
        return (
            <div className="report-box">
                <div className="box-header">
                    <h1>Cases by Day of Week</h1>
                </div>
                <div className="box-body">
                    <div className="chart-container" style={this.getCanvasContainerStyle()}>
                        <canvas id="casesByDay"></canvas>
                    </div>
                </div>
            </div>
        );
    }
}
 
export default CasesByDayOfWeek;
