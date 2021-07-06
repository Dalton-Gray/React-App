import React from 'react';

class Login extends React.Component {

  render() {
    return (
      <div>
        <input
          className="loginForm"
          type='enail'
          placeholder='email'
          value={this.props.email}
          onChange={this.props.handleEmail}
        />
        <input
          className="loginForm"
          type='password'
          placeholder='password'
          value={this.props.password}
          onChange={this.props.handlePassword}
        />
        <button className="loginButton" onClick={this.props.handleLoginClick}>Log in</button>
      </div>
    );
  }
}

export default Login;