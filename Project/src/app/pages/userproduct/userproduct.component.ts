import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ProductService } from '../../Service/product.service';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
@Component({
  selector: 'app-userproduct',
  imports: [CommonModule],
  templateUrl: './userproduct.component.html',
  styleUrl: './userproduct.component.css'
})
export class UserproductComponent implements OnInit {
  productResponseList : any = [];
  searchResponseList: any = [];
  cartList:any=[];
  selectedProduct: any = null;
cartCount: number = 0;
  constructor(private _productService :ProductService, private _toasterService : ToastrService, private _route:Router) { }


ngOnInit(): void {
  this.getAllProduct();
    const count = localStorage.getItem('cartCount');
  this.cartCount = count ? JSON.parse(count) : 0;
}
getAllProduct(){
this._productService.getProduct().subscribe({
  next:(response:any)=>{
      debugger
      this.productResponseList =response[0];
      this.searchResponseList=response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
// addToCart(product: any){
//   this._route.navigateByUrl('/pages/cart');
// }

openModal(product: any) {
  this.selectedProduct = product;
  const modalElement = document.getElementById('productModal');
  if (!modalElement) {
    return;
  }
  const bootstrapModal = new (window as any).bootstrap.Modal(modalElement);
  bootstrapModal.show();
}
addtoCart(product: any) {
  const existingCart = localStorage.getItem('cart');
  const count = localStorage.getItem('cartCount');
  this.cartCount = count ? JSON.parse(count) : 0;
  if (existingCart) {
    this.cartList = JSON.parse(existingCart);
  }
  if (this.cartList.product_Id !== product.product_Id){
    if (!Array.isArray(this.cartList)) {
    this.cartList = [this.cartList];
  }
    this.cartList.push(product);
  }
  this.cartCount++;
    localStorage.setItem('cartCount', JSON.stringify(this.cartCount));
  localStorage.setItem('cart', JSON.stringify(this.cartList));
}
goToCart() {
  console.log("Navigate to cart page");
}
}
