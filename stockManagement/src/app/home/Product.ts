import { Category } from "./Category"

export class Product{
    price?:any
    quantity?:any
    brand?:any
    name?:any
    category:Category=new Category
    category_id:any
}