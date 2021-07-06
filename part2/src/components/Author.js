import React from 'react';
import Content from './Content.js';


class Author extends React.Component {

  state = { display: false, data: [] }

  loadFilmDetails = () => {
    console.log(this.props.details)
    const url = "http://unn-w17021399.newnumyspace.co.uk/KF6012/part1/api/content?authorId=" + this.props.details.authorId
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

  handleActorClick = (e) => {
    this.setState({ display: !this.state.display })
    this.loadFilmDetails()
  }

  render() {

    let content = ""
    if (this.state.display) {
      content = this.state.data.map((details, i) => (<Content key={i} details={details} />))
    }

    return (
      <div>
        <h2 className="collapsible" onClick={this.handleActorClick}>{this.props.details.name}</h2>
        {content}
      </div>
    );
  }
}

export default Author;