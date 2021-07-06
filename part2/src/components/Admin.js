import React from 'react';
import Login from './Login.js';
import Update from './Update.js';

class Admin extends React.Component {

  constructor(props) {
    super(props);
    this.state = { "authenticated": false, "email": "", "password": "" }

    this.handleEmail = this.handleEmail.bind(this);
    this.handlePassword = this.handlePassword.bind(this);
  }

  state = { "authenticated": false, "email": "", "password": "" }

  postData = (url, myJSON, callback) => {
    fetch(url, {
      method: 'POST',
      headers: new Headers(),
      body: JSON.stringify(myJSON)
    })
      .then((response) => response.json())

      .then((data) => {
        callback(data)
      })
      .catch((err) => {
        console.log("something went wrong ", err)
      }
      );
  }

  componentDidMount() {
    if (localStorage.getItem('myToken')) {
      this.setState({ "authenticated": true });
    }
  }

  loginCallback = (data) => {
    if (data.status === 200) {
      this.setState({ "authenticated": true })
      localStorage.setItem('myToken', data.token);
    }
  }

  updateCallback = (data) => {
    if (data.status !== 200) {
      this.setState({ "authenticated": false })
      localStorage.removeItem('myToken');
    }
  }

  handleLoginClick = () => {
    const url = "http://unn-w17021399.newnumyspace.co.uk/KF6012/part1/api/login"
    let myJSON = {
      "email": this.state.email,
      "password": this.state.password,
    }
    this.postData(url, myJSON, this.loginCallback)
  }

  parseJwt(token) {
    let base64Payload = token.split('.')[1];
    let payload = Buffer.from(base64Payload, 'base64');
    return JSON.parse(payload.toString());
  }

  checkJwtExpiry(exp) {
    if (Date.now() >= exp * 1000) {
      return false;
    }
    return true;
  }

  handleUpdateClick = (sessionId, name) => {
    const url = "http://unn-w17021399.newnumyspace.co.uk/KF6012/part1/api/update-session-name"
    const isAdmin = this.parseJwt(localStorage.getItem('myToken')).admin
    const tokenExp = this.parseJwt(localStorage.getItem('myToken')).exp

    if (this.checkJwtExpiry(tokenExp)) {
      if (localStorage.getItem('myToken')) {
        if (isAdmin == 1) {
          let myToken = localStorage.getItem('myToken')
          let myJSON = {
            "token": myToken,
            "sessionId": sessionId,
            "name": name,
          }
          this.postData(url, myJSON, this.updateCallback)
          window.alert("Session name updated.")
        } else {
          window.alert("Admin status required.")
        }
      } else {
        this.setState({ "authenticated": false })
      }
    } else {
      window.alert("Session expired.")
      this.setState({ "authenticated": false })
      localStorage.removeItem('myToken');
    }
  }

  handleLogoutClick = () => {
    this.setState({ "authenticated": false })
    localStorage.removeItem('myToken');
  }

  handlePassword = (e) => {
    this.setState({ password: e.target.value })
  }
  handleEmail = (e) => {
    this.setState({ email: e.target.value })
  }

  render() {
    let page = <Login handleLoginClick={this.handleLoginClick} email={this.state.email} password={this.props.password} handleEmail={this.handleEmail} handlePassword={this.handlePassword} />
    if (this.state.authenticated) {
      page = <div>
        <button className="logoutButton" onClick={this.handleLogoutClick}>Log out</button>
        <Update handleUpdateClick={this.handleUpdateClick} />
      </div>
    }

    return (
      <div>
        <h1>Admin</h1>
        {page}
      </div>
    );
  }
}

export default Admin;