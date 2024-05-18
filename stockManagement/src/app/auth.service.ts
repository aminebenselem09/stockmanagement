import { Injectable } from '@angular/core';
import { JwtHelperService, JWT_OPTIONS  } from '@auth0/angular-jwt';

providers: [
  { provide: JWT_OPTIONS, useValue: JWT_OPTIONS },
  JwtHelperService
]
@Injectable({
  providedIn: 'root',

})

export class AuthService {

  constructor(public jwtHelper: JwtHelperService) {}
  public isAuthenticated(): boolean {
    const token = localStorage.getItem('token');
    // Check whether the token is expired and return
    // true or false
    return !this.jwtHelper.isTokenExpired(token)||!(token=='');
  }
}