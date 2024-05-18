import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { 
  AuthGuardService as AuthGuard 
} from './auth-guard.service';
import { 
  RoleGuardService as RoleGuard 
} from './role-guard.service';
import { ShopComponent } from './shop/shop.component';
import { CartComponent } from './cart/cart.component';
import { OrdersComponent } from './orders/orders.component';
const routes: Routes = [
  {path:"login",component:LoginComponent},
  { 
    
    path: '', 
    redirectTo: '/login', 
    pathMatch: 'full' 
  },
  {path:"admin",component:HomeComponent, canActivate:[AuthGuard,RoleGuard], 
  data: { 
    expectedRole: 'admin'
  }},
  {path:"shop",component:ShopComponent, canActivate:[AuthGuard,RoleGuard], 
  data: { 
    expectedRole: 'user'
  }},
  {path:"orders",component:OrdersComponent, canActivate:[AuthGuard,RoleGuard], 
  data: { 
    expectedRole: 'admin'
  }},
  {path:"cart",component:CartComponent, canActivate:[AuthGuard,RoleGuard], 
  data: { 
    expectedRole: 'user'
  }}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
