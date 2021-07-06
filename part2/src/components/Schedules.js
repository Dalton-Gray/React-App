import React from 'react';
import SelectDay from './SelectDay.js';
import Search from './Search.js'
import Schedule from './Schedule.js'

class Schedules extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      page: 1,
      pageSize: 10,
      query: "",
      data: [],
      dayString: ""
    }
  }

  componentDidMount() {
    const url = "http://unn-w17021399.newnumyspace.co.uk/KF6012/part1/api/schedule"
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        this.setState({ data: data.data })
      })
      .catch((err) => {
        console.log("something went wrong ", err)
      }
      );
  }

  handlePreviousClick = () => {
    this.setState({ page: this.state.page - 1 })
  }

  handleNextClick = () => {
    this.setState({ page: this.state.page + 1 })
  }

  handleSearch = (e) => {
    this.setState({ page: 1, query: e.target.value })
  }

  searchString = (s) => {
    return s.toLowerCase().includes(this.state.query.toLowerCase())
  }

  searchDetails = (details) => {
    return ((this.searchString(details.name)))
  }

  handleSelect = (e) => {
    this.setState({ dayString: e.target.value })
  }

  selectDetails = (details) => {
    return ((this.state.dayString === details.dayString) || (this.state.dayString === ""))
  }

  render() {

    let filteredData = (
      this.state.data
        .filter(this.selectDetails)
        .filter(this.searchDetails)
    )

    let noOfPages = Math.ceil(filteredData.length / this.state.pageSize)
    if (noOfPages === 0) { noOfPages = 1 }
    let disabledPrevious = (this.state.page <= 1)
    let disabledNext = (this.state.page >= noOfPages)

    return (
      <div>
        <h1>Schedule</h1>
        <SelectDay dayString={this.state.dayString} handleSelect={this.handleSelect} />
        <Search query={this.state.query} handleSearch={this.handleSearch} />
        <div className="grid-container">
          {
            filteredData
              .slice(((this.state.pageSize * this.state.page) - this.state.pageSize), (this.state.pageSize * this.state.page))
              .map((details, i) => (<Schedule key={i} details={details} />))
          }
        </div>
        <button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
          Page {this.state.page} of {noOfPages}
        <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button>
      </div>
    );
  }
}

export default Schedules;
