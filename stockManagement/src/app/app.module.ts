import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { LoginComponent } from './login/login.component';
import { JwtModule, JwtModuleOptions } from '@auth0/angular-jwt';
import { RoleGuardService } from './role-guard.service';
import { ShopComponent } from './shop/shop.component';
import { CartComponent } from './cart/cart.component';
import { OrdersComponent } from './orders/orders.component';
const JWT_Module_Options: JwtModuleOptions = {
  config: {
 },
};
@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    ShopComponent,
    CartComponent,
    OrdersComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule ,
    JwtModule.forRoot(JWT_Module_Options),


  ],
  providers: [RoleGuardService],
  bootstrap: [AppComponent]
})
export class AppModule { }
