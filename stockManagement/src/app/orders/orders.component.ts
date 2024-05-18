import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-orders',
  templateUrl: './orders.component.html',
  styleUrls: ['./orders.component.css']
})
export class OrdersComponent implements OnInit {
responses:any=[]

  url:any="http://localhost:9090"
  token:any=localStorage.getItem('token')
  header=new HttpHeaders()
  .set("Authorization","Bearer "+this.token);
constructor(private http:HttpClient,private router:Router) { }

  ngOnInit(): void {
    this.getAllOrders();

  }
getAllOrders(){
  this.http.get(this.url +"/getorders",{headers:this.header}) .subscribe({
    next: (res) => {this.responses=res,console.log(res);
      
      
    },
  error: (err) => console.log(err),
  complete: () => console.log("")
  
  });

}
deleteOrder(event:any){
  let id=event.target.id;
  this.http.delete(this.url +"/delorder?id="+id,{headers:this.header}) .subscribe({
    next: (res) => {console.log(res);window.location.reload()
      
      
    },
  error: (err) => console.log(err),
  complete: () => console.log("")
  
  });
}

updateOrder(event:any){
  let id=event.target.id;
  
  this.http.put(this.url +"/updateorder?id="+id,null,{headers:this.header,responseType:'text'}) .subscribe({
    next: (res) => {console.log(res);window.location.reload()
      
      
    },
  error: (err) => console.log(err),
  complete: () => console.log("")
  
  });
  
}
}
