import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Product } from '../home/Product';
import { Router } from '@angular/router';

@Component({
  selector: 'app-shop',
  templateUrl: './shop.component.html',
  styleUrls: ['./shop.component.css']
})
export class ShopComponent implements OnInit {
   myProduct:any
  responses:any=[]
  categories:any=[]
  products:any=[]
  Presponses:any=[]
  url:any="http://localhost:9090"
  token:any=localStorage.getItem('token')
  header=new HttpHeaders()
  .set("Authorization","Bearer "+this.token);
  constructor(private http:HttpClient,private router:Router) { }

  ngOnInit(): void {
    this.getAllProducts()
  }
  getAllCategories(){
    this.http.get(this.url+"/categories",{headers:this.header}) .subscribe({
      next: (res) => {this.responses=res;console.log("res")
        for(let i=0;i<this.responses.length;i++){
         this.categories.push(this.responses[i])
        }
        
      },
    error: (err) => console.log(err),
    complete: () => console.log("")
    
    });
    }
    
    getAllProducts(){
      this.http.get(this.url +"/products",{headers:this.header}) .subscribe({
        next: (res) => {this.Presponses=res,console.log(res);
          for(let i=0;i<this.Presponses.length;i++){
            this.products.push(this.Presponses[i])
           }
          
        },
      error: (err) => {console.log(err);this.logout()},
      complete: () => console.log("")
      
      });
      } 
      addToCart(event:any){
        console.log(event.target.id);
        
        for(let i=0;i<this.products.length;i++){
          if(event.target.id==this.products[i].id){
            this.myProduct=this.products[i]
          }
             }
             let data=JSON.parse( JSON.stringify(this.myProduct));
             this.http.post(this.url+"/order?uid="+localStorage.getItem('id')+"&pid="+event.target.id,data,{headers:this.header,responseType:'text'}).subscribe({
              next: (res) => {alert("produit commandé"),console.log(res)
                
                
              },
            error: (err) =>{ console.log(err),alert("error")},
            complete: () => console.log("")
            
            });


      }
      logout(){
        localStorage.clear()
      this.router.navigate(['/login'])
      }
}
