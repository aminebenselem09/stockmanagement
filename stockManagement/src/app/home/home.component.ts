import { Component, OnInit } from '@angular/core';
import { Modal } from 'bootstrap';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Category } from './Category';
import { Product } from './Product';
import { Router } from '@angular/router';


@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  
  searchtext:any
  searched:any=[]
  backup:any=[]
  price:any
  quantity:any
  id:any
  catfilter: Category = new Category; 
  products:any=[]
  Presponses?:any=[]
  categories:any=[]
  responses?:any=[];
    myModal:any
    modal2:any
    product:Product =new Product
    url:any="http://localhost:9090"
    token:any=localStorage.getItem('token')
    header=new HttpHeaders()
    .set("Authorization","Bearer "+this.token);
  constructor(private http:HttpClient,private router:Router) { }

  ngOnInit(): void {
    
    this.backup=this.products

    var element = document.getElementById('user-form-modal') as HTMLElement;
    var element2 = document.getElementById('editproductmodal') as HTMLElement;

    this.myModal = new Modal(element);
    this.modal2 = new Modal(element2)
    this.getAllCategories()
    this.getAllProducts()
     }

getAllCategories(){
this.http.get(this.url+"/categories",{headers:this.header}) .subscribe({
  next: (res) => {this.responses=res;console.log(res)
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
  error: (err) => console.log(err),
  complete: () => console.log("")
  
  });
  }

getCatFilter(event:any){
  if(event.target.value=="All"){this.products=this.backup}
  
  else{ let selectedOption = event.target.options[event.target.selectedIndex];
    let  optionId = selectedOption.getAttribute('id');
  
    
      this.http.post(this.url+"/catfilter?id="+optionId,null,{headers:this.header}).subscribe({
        next: (res) => {this.products=res,console.log(res);
          
          
        },
      error: (err) => console.log(err),
      complete: () => console.log("")
      
      });}
 
}
onChange(event:any){
  let selectedOption = event.target.options[event.target.selectedIndex];
  this.product.category.id = selectedOption.getAttribute('id');
  this.product.category_id= selectedOption.getAttribute('id');

  this.product.category.name=event.target.value
  console.log(this.product)
  }
onSubmit(){
  let data=JSON.parse( JSON.stringify(this.product));
  this.http.post(this.url+"/addproduct",data,{headers:this.header}).subscribe({
    next: (res) => {alert("produit ajouté"),console.log(res),window.location.reload();
      
      
    },
  error: (err) =>{ console.log(err),alert("error")},
  complete: () => console.log("")
  
  });
}
update(){
  console.log(this.id)
  this.http.put(this.url+'/update?id='+ this.id+'&price='+this.price+'&quantity='+this.quantity,null,{headers:this.header}).subscribe({
  next: (res) => {console.log(res);window.location.reload()
    
    
  },
error: (err) =>{ console.log(err),alert("error")},
complete: () => console.log("")

});
}
Edit(event:any){
this.id=event.target.id


}

   openModal(){
     this.myModal.show();
   }
   openModal2(){
    this.modal2.show();
  }
   
   
   onCloseHandled(){
     this.myModal.hide();
     
}

onCloseHandled2(){
  this.modal2.hide();
  
}
delete(event:any){
this.id=event.target.id

this.http.delete(this.url +"/deleteproduct?id="+this.id,{headers:this.header}).subscribe({
  next: (res) => { window.location.reload()},
error: (err) =>{ console.log(err),alert("error")},
complete: () => console.log("")

});
}

Recherche(event:any){

  let text=this.searchtext
  this.products=this.backup
  this.searched=[]

  this.products.forEach((prod: Product)  => {
    if(prod.name?.toLocaleLowerCase().includes(text.toLocaleLowerCase())){
     this.searched.push(prod)

   }
  });
this.products=this.searched


}
logout(){
  localStorage.clear()
this.router.navigate(['/login'])
}

}