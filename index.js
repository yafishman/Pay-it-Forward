import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';

class Login extends React.Component {
  onLoginSubmit = (event) => {
    event.preventDefault();
    let username = document.getElementById('username').value;
    let password = document.getElementById('password').value;
    let zipcode = document.getElementById('zipcode').value;
    let data = {'username': username, 'password': password, 'zipcode': zipcode};
    //bypasses the CORS issue (help from TA) and uses checkLogin file to see if the information given is correct to login
    const proxyurl = "https://cors-anywhere.herokuapp.com/";
    fetch(proxyurl+"http://ec2-3-17-71-131.us-east-2.compute.amazonaws.com/~yafishman/code/checkLogin.php", {
      method: 'POST',
      body: JSON.stringify(data),
      headers: {'content-type': 'application/json'}
    })
    .then(response => response.json())
    .then(data => {
      if (data.success===false){
        alert("Cannot Log In");
      }
      else{
        //if the user can log in, they are directed to a full version of the page with all sections rendered
        let newWin = window.open("http://ec2-3-17-71-131.us-east-2.compute.amazonaws.com/~yafishman/code/Pay_It_Forward.html");
        newWin.name = (username+zipcode);
        window.close();
      }
    })
}
onRegisterSubmit = (event) => {
  event.preventDefault();
  let username = document.getElementById('username').value;
  let password = document.getElementById('password').value;
  let zipcode = document.getElementById('zipcode').value;
  let data = {'username': username, 'password': password, 'zipcode': zipcode};
      //bypasses the CORS issue (help from TA) and uses registerUser file to see if the information given is correct to register (ie the account doesn't exist and all fields are filled)
  const proxyurl = "https://cors-anywhere.herokuapp.com/";
  fetch(proxyurl+"http://ec2-3-17-71-131.us-east-2.compute.amazonaws.com/~yafishman/code/registerUser.php", {
    method: 'POST',
    body: JSON.stringify(data),
    headers: {'content-type': 'application/json'}
  })
  .then(response => response.json())
  .then(data => {
    if (data.success===false){
      alert("Cannot Create Account");
    }
    else{
        //if the user can register, they are directed to a full version of the page with all sections rendered
      let newWin = window.open("http://ec2-3-17-71-131.us-east-2.compute.amazonaws.com/~yafishman/code/Pay_It_Forward.html");
      newWin.name = username+" "+zipcode;
    }
  })
}
//allows unregistered users to login
unregisteredSubmit = (event) => {
  event.preventDefault();
  let newWin = window.open("http://ec2-3-17-71-131.us-east-2.compute.amazonaws.com/~yafishman/code/Pay_It_Forward.html");
}
//renders the HTML for the login section
  render() {
    return (
      <div>
      <h1>Pay It Forward</h1>
      <div className="grid-container">
      <div className="grid-item login" id="logIn">
        Username: <input type="text" id= "username"  name="username"/>
        Password: <input type="text" id= "password" name="password"/>
        Current ZipCode: <input type="text" id= "zipcode" name="zipcode"/>
        <input onClick={this.onLoginSubmit} type="submit" id="login" value="Sign In"/>
        <input onClick={this.onRegisterSubmit} type="submit" id="register" value="Sign Up"/>
        <input onClick={this.unregisteredSubmit} type="submit" id="noRegister" value="URS"/>
      </div>
    </div>
    </div>
    );
  }
}
//when the page opened, the login component is rendered which starts the rest of the rendering process
ReactDOM.render(
  <Login />,
  document.getElementById('root')
);
