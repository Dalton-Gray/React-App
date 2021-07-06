import React from 'react';

class UpdateItem extends React.Component {

  state = { name: this.props.details.name }

  handleNameChange = (e) => {
    this.setState({ name: e.target.value })
  }

  handleNameUpdate = () => {
    this.props.handleUpdateClick(this.props.details.sessionId, this.state.name)
  }

  render() {
    return (
      <div>
        <h2>{this.props.details.sessionId}</h2>
        <textarea
          rows="4" cols="5"
          value={this.state.name}
          onChange={this.handleNameChange}
        />
        <button className="updateButton" onClick={this.handleNameUpdate}>Update</button>
      </div>
    );
  }
}

export default UpdateItem;