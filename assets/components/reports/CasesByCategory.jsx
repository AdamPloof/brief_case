import React, { Component } from 'react';
import Chart from 'chart.js/auto';

class CasesByCategory extends Component {
    constructor(props) {
        super(props);
        this.state = {
            startDate: null,
            endDate: null,
            labels: [],
            chartData: [],
            loading: true,
        }
        this.chart = null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.props.data != prevProps.data) {
            this.setChartData();
        }

        if (this.state.chartData != prevState.chartData && this.state.chartData.length != 0) {
            this.updateChart();
        }
    }

    setChartData() {
        const labels = this.props.data.map(stats => {
            return stats.category;
        });
        const chartData = this.props.data.map(stats => {
            return stats.ratio;
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

    makeDataset() {
        const data = [...this.state.chartData];
        const colors = [];

        for (let categoryData of this.props.data) {
            colors.push(this.props.categoryColorMap.get(categoryData.category));
        }

        return {
            label: 'Cases by Category',
            backgroundColor: colors,
            data,
            hoverOffset: 4
        };
    }

    drawChart() {
        const labels = [...this.state.labels];
        const data = {
            labels: labels,
            datasets: [this.makeDataset()]
          };

        const config = {
            type: 'doughnut',
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
        
        const ctx = document.getElementById('casesByCategory');
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
                    <h1>Cases by Category</h1>
                </div>
                <div className="box-body">
                    <div className="chart-container" style={this.getCanvasContainerStyle()}>
                        <canvas id="casesByCategory" width="384" height="768"></canvas>
                    </div>
                </div>
            </div>
        );
    }
}
 
export default CasesByCategory;
