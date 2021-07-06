import React from 'react';

class SelectDay extends React.Component {
  render() {
    return (
      <label>
        Day:
        <select value={this.props.dayString} onChange={this.props.handleSelect}>
          <option value="">Any</option>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
        </select>
      </label>
    )
  }
}


export default SelectDay;