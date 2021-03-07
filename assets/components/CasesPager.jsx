import React, { Component } from 'react';

class CasesPager extends Component {
    render() { 
        return (
            <div className="pager">
                <ul className="cases-pager-list">
                    <li>
                        <a 
                            href="#"
                            className={this.props.page == 1 ? 'disabled' : ''}
                            onClick={(e) => {
                                e.preventDefault();
                                if (this.props.page == 1) {
                                    return;
                                }
                                this.props.changePage(1)
                            }}
                        >
                            &lt;&lt; First
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            className={this.props.page == 1 ? 'disabled' : ''}
                            onClick={(e) => {
                                e.preventDefault();
                                if (this.props.page == 1) {
                                    return;
                                }
                                this.props.changePage(this.props.page - 1)
                            }}
                        >
                            &lt; Prev
                        </a>
                        
                    </li>
                    <li>Page {this.props.page}</li>
                    <li>
                        <a 
                            href="#"
                            className={this.props.page == this.props.pages ? 'disabled' : ''}
                            onClick={(e) => {
                                e.preventDefault();
                                if (this.props.page == this.props.pages) {
                                    return;
                                }
                                this.props.changePage(this.props.page + 1)
                            }}
                        >
                            Next &gt;
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            className={this.props.page == this.props.pages ? 'disabled' : ''}
                            onClick={(e) => {
                                e.preventDefault();
                                if (this.props.page == this.props.pages) {
                                    return;
                                }
                                this.props.changePage(this.props.pages)
                            }}
                        >
                            Last &gt;&gt;
                        </a>
                    </li>
                </ul>
            </div>
        );
    }
}
 
export default CasesPager;
