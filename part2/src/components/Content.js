import React from 'react';

class Content extends React.Component {

  state = {
    display: false,
    displayFurther: false
  }

  handleDisplayClick = () => {
    this.setState({ display: !this.state.display })
  }

  handleDetailClick = () => {
    this.setState({ displayFurther: !this.state.displayFurther })
  }

  render() {
    let info = "";
    let furtherInfo = "";

    if (this.state.displayFurther) {
      furtherInfo = <p>Award: {this.props.details.award}</p>
    }

    if (this.state.display) {
      info = <div className="content">
        <div className="content2">
          <p onClick={this.handleDetailClick}>{this.props.details.abstract}</p>
          <div className="content3">
            {furtherInfo}
          </div>
        </div>
      </div>
    }

    return (
      <div>
        <p className="content" onClick={this.handleDisplayClick}>Title: {this.props.details.title}</p>
        {info}
      </div>
    );
  }
}



export default Content;