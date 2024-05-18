import { Injectable } from '@angular/core';
import { 
  Router,
  CanActivate,
  ActivatedRouteSnapshot
} from '@angular/router';
import { AuthService } from './auth.service';
@Injectable()
export class RoleGuardService implements CanActivate {
  constructor(public auth: AuthService, public router: Router) {}
  canActivate(route: ActivatedRouteSnapshot): boolean {
    
    const expectedRole = route.data['expectedRole'];
   
    if (
      !this.auth.isAuthenticated() || 
      localStorage.getItem('role')!= expectedRole
    ) {
      this.router.navigate(['login']);
      return false;
    }
    return true;
  }
}