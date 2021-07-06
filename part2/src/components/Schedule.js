import React from 'react';

class Schedule extends React.Component {

  state = {
    display: false,
    displayFurtherInfo: false
  }

  handleScheduleClick = () => {
    this.setState({ display: !this.state.display })
  }
  handleDetailClick = () => {
    this.setState({ displayFurtherInfo: !this.state.displayFurtherInfo })
  }

  render() {

    let info = "";
    let furtherInfo = "";

    if (this.state.displayFurtherInfo) {
      furtherInfo = <div className="content2">
        <p>Time: {this.props.details.startHour}:{this.props.details.startMinute} - {this.props.details.endHour}:{this.props.details.endMinute} </p>
        <p>Room: {this.props.details.room}</p>
        <p>Type: {this.props.details.sessionType}</p>
        <p>Session Chair: {this.props.details.sessionChair}</p>
      </div>
    }

    if (this.state.display) {
      info = <div className="content">
        <p onClick={this.handleDetailClick}>Day: {this.props.details.dayString}</p>
        {furtherInfo}
      </div>
    }

    return (
      <div className="wrapper">
        <p className="collapsible" onClick={this.handleScheduleClick}>{this.props.details.name}</p>
        {info}
      </div>
    );
  }
}


export default Schedule;