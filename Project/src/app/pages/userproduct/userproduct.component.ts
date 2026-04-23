import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
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
  cartitem:number=1;

  selectedProduct: any = null;
cartCount: number = 0;
  constructor(private _productService :ProductService, private _toasterService : ToastrService, private _route:Router) { }


ngOnInit(): void {
  this.getAllProduct();
    const count = localStorage.getItem('cartCount');
  this.cartCount = count ? JSON.parse(count) : 0;
 this.getcart();
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
// addToCart(product: any){\p
//   this._route.navigateByUrl('/pages/cart');
// }
getcart(){
  const cartData = localStorage.getItem('cart');

  if (cartData) {
    this.cartList = JSON.parse(cartData);
    if (!Array.isArray(this.cartList)) {
      this.cartList = [this.cartList];
    }
    this.cartList = this.cartList.map((item: any) => ({
      ...item,
      checked: item.checked ?? false
    }));
  } else {
    this.cartList = [];
  }
}
openModal(product: any) {
  this.selectedProduct = product;
  const modalElement = document.getElementById('productModal');
  if (!modalElement) {
    return;
  }
  const bootstrapModal = new (window as any).bootstrap.Modal(modalElement);
  bootstrapModal.show();
}
// isProductAssigned(productId: number): boolean {
//   return this.cartList.some((item: any) => item.product_Id === productId);
// }
addtoCart(product: any) {
  const existingCart = localStorage.getItem('cart');
  const count = localStorage.getItem('cartCount');
  this.cartCount = count ? JSON.parse(count) : 0;

  if (existingCart) {
    this.cartList = JSON.parse(existingCart);
    if (!Array.isArray(this.cartList)) {
      this.cartList = [this.cartList];
    }
  } else {
    this.cartList = [];
  }

  const existingItem = this.cartList.find((item: any) => item.product_Id === product.product_Id);
  if (existingItem) {
    existingItem.cartitem = (existingItem.cartitem || 1) + 1;
     if (existingItem.checked === undefined) {
      existingItem.checked = false;
    }
  } else {
    this.cartList.push({ ...product, cartitem: 1, checked: false   });
  }

  this.cartCount = this.cartList.reduce((total: number, item: any) => total + (item.cartitem || 0), 0);
  localStorage.setItem('cartCount', JSON.stringify(this.cartCount));
  localStorage.setItem('cart', JSON.stringify(this.cartList));
}
goToCart() {
  console.log("Navigate to cart page");
}
increaseQty(item:any) {
  const Id = item.product_Id;
  const new1:any=[]
  this.cartList.map((item: any) => {

    if(item.product_Id === Id){
      item.cartitem += 1;
      this.cartCount++;
    }
    return new1.push(item)
  });
  console.log(new1)
      localStorage.setItem('cart', JSON.stringify(new1));

}  

toggleItemSelection(event: any, item: any) {
    item.checked = event.target.checked;
    localStorage.setItem('cart', JSON.stringify(this.cartList));
  }

  getCheckedItems() {
    return this.cartList.filter((item: any) => item.checked);
  }

  getCheckedSubtotal() {
    return this.getCheckedItems().reduce((total: number, item: any) => {
      return total + (item.price * item.cartitem);
    }, 0);
  }

  getCheckedTaxAmount() {
    const subtotal = this.getCheckedSubtotal();
    return Math.round(subtotal * 0.05);
  }

  getCheckedTotal() {
    return this.getCheckedSubtotal() + this.getCheckedTaxAmount();
  }

  getCheckTotal() {
    return this.getCheckedSubtotal();
  }

// Decrease quantity
decreaseQty(item: any) {
     const Id = item.product_Id;
  const new1:any=[]
  this.cartList.map((item: any) => {

    if(item.product_Id === Id){
      item.cartitem -= 1;
      this.cartCount--;
    }
    return new1.push(item)
  });
  console.log(new1)
      localStorage.setItem('cart', JSON.stringify(new1));
      localStorage.setItem('cartCount', JSON.stringify(this.cartCount));
}


getTotal() {
  return this.cartList.reduce((total:any, item:any) => {
    return total + (item.price * item.qty);
  }, 0);
}


getTotalPrice() {
  return this.cartList.reduce((total: number, item: any) => {
    return total + (item.price * item.cartitem);
  }, 0);
}

getTaxAmount() {
  const subtotal = this.getTotalPrice();
  return Math.round(subtotal * 0.05); // 5% tax
}


removeItem(item: any) {
  const index = this.cartList.findIndex((cartItem: any) => cartItem.product_Id === item.product_Id);
  if (index > -1) {
    this.cartCount -= item.cartitem;
    this.cartList.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(this.cartList));
    localStorage.setItem('cartCount', JSON.stringify(this.cartCount));
  }
}

clearCart() {
  this.cartList = [];
  this.cartCount = 0;
  localStorage.removeItem('cart');
  localStorage.removeItem('cartCount');
}
}
