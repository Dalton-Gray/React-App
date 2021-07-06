import React from 'react';
import { BrowserRouter as Router, Switch, Route, NavLink } from "react-router-dom";
import Schedules from './components/Schedules.js';
import Authors from './components/Authors.js';
import Admin from './components/Admin.js'
import NotFound404 from './components/NotFound404.js';
import './App.css';

function App() {
  return (
    <Router basename="/KF6012/part2/">
      <div className="App">
        <div>
          <nav>
            <ul>
              <li>
                <NavLink activeClassName="selected" to="/">Schedule</NavLink>
              </li>
              <li>
                <NavLink activeClassName="selected" to="/Authors">Authors</NavLink>
              </li>
              <li>
                <NavLink activeClassName="selected" to="/admin">Admin</NavLink>
              </li>
            </ul>
          </nav>
          <Switch>
            <Route exact path="/">
              <Schedules />
            </Route>
            <Route path="/Authors">
              <Authors />
            </Route>
            <Route path="/admin">
              <Admin />
            </Route>
            <Route path="*">
              <NotFound404 />
            </Route>
          </Switch>
        </div>
      </div>
    </Router>
  );
}

export default App;