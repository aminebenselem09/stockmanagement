import { Component, OnInit } from '@angular/core';
import { User } from './User';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
 
  
  constructor(private http:HttpClient ,private router :Router) { }
  url:any="http://localhost:9090"
  jwt:any
user:User=new User
  ngOnInit(): void {
    if (localStorage.getItem('role')=='user'){
      this.router.navigate(['/shop'])

    }else { this.router.navigate(['/admin'])}





    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');
    
    signUpButton?.addEventListener('click', () => {
      container?.classList.add("right-panel-active");
    });
    
    signInButton?.addEventListener('click', () => {
      container?.classList.remove("right-panel-active");
    });



    }
    
SignUp(){
  this.http.post(this.url+"/signup",this.user).subscribe({
    next: (res) => {console.log(res);document.getElementById('container')?.classList.remove("right-panel-active");},
  error: (err) =>{ console.log(err),alert("error")},
  complete: () => console.log("")
  
  });
}
SignIn(){
  this.user.email=""
  let data=JSON.parse( JSON.stringify(this.user));
  console.log(data)
  this.http.post(this.url+"/signin",data,{responseType:'json'}).subscribe({
    next: (res) => {console.log(res),this.jwt=res,localStorage.setItem('token',this.jwt?.token),localStorage.setItem('role',this.jwt?.role),localStorage.setItem('id',this.jwt?.id)
    
      if (this.jwt.role=='user'){
        this.router.navigate(['/shop'])

      }else { this.router.navigate(['/admin'])}
     },
  error: (err) =>{ console.log(err),alert(err.message)},
  complete: () => console.log("")
  
  });
}
    


  

}
