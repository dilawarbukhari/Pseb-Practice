import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
import { ProductService } from '../../Service/product.service';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { UserProductService } from '../../Service/userproduct.service';
import { CategoryService } from '../../Service/category.service';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
@Component({
  selector: 'app-userproduct',
  imports: [CommonModule, ReactiveFormsModule,FormsModule
    
  ],
  templateUrl: './userproduct.component.html',
  styleUrl: './userproduct.component.css'
})
export class UserproductComponent implements OnInit {
   shippingForm!  :FormGroup;
  productResponseList : any = [];
  searchResponseList: any = [];
  filteredProductList: any = [];
 
  cartList:any=[];
  cartitem:number=1;
  categoryResponseList:any = [];
  searchText: string = '';
  selectedCategory: string = '';

  selectedProduct: any = null;
cartCount: number = 0;
  constructor(private _Fb:FormBuilder,private _productService :ProductService,private _userProduct: UserProductService, private _toasterService : ToastrService, private _route:Router,private _categoryService :CategoryService) { }


ngOnInit(): void {
  this.getAllProduct();
  this.SetValidation();
  this.getAllCategories();
    const count = localStorage.getItem('cartCount');
  this.cartCount = count ? JSON.parse(count) : 0;
 this.getcart();
 
}

SetValidation(){
this.shippingForm = this._Fb.group({
shippingAddress: ['',Validators.required],
shippingCity: ['',Validators.required],
shippingPostalCode: ['',Validators.required],
shippingPhone: ['',Validators.required]
})
}
getAllCategories(){
this._categoryService.getCategories().subscribe({
  next:(response:any)=>{
    debugger
      this.categoryResponseList =response[0];    
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
getAllProduct(){
this._productService.getProduct().subscribe({
  next:(response:any)=>{
      debugger
      this.productResponseList =response[0];
      this.searchResponseList=response[0];
      this.filteredProductList=response[0];
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
orderPlace(){
  if (this.shippingForm.invalid) {
    this.shippingForm.markAllAsTouched();
    return;
  }
  const ShippingDetails = this.shippingForm.value;
  const items = this.getCheckedItems();
  const totalAmount = this.getCheckedTotal();
  const order={
    items: items,
    totalAmount: totalAmount,
    ShippingDetails: ShippingDetails
  };
  
  if (items.length === 0) {
    this._toasterService.warning('Please select at least one item to place an order.', 'No Items Selected');
    return;
  }
  this._userProduct.orderPlace(order).subscribe({
    next:(response:any)=>{
      debugger
     if(response[1].status !== 200){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
      this.shippingForm.reset();
      this.cartList = this.cartList.filter((item: any) => !item.checked);
      localStorage.setItem('cart', JSON.stringify(this.cartList));
      this.cartCount = this.cartList.reduce((total: number, item: any) => total + (item.cartitem || 0), 0);
      localStorage.setItem('cartCount', JSON.stringify(this.cartCount));
        
},
error:(error)=>{
  if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Order Failed');
  }
}
  })
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

searchProducts(event: any) {
  this.searchText = event.target.value.toLowerCase();
  this.applyFilters();
}

filterByCategory(event: any) {
  debugger
  this.selectedCategory = event.target.value;
  this.applyFilters();
}

applyFilters() {
  debugger
  this.filteredProductList = this.productResponseList.filter((product: any) => {
    debugger
    const matchesSearch = product.product_name.toLowerCase().includes(this.searchText) ||
                          product.description.toLowerCase().includes(this.searchText);
                          debugger
    const matchesCategory = !this.selectedCategory || product.category_id == this.selectedCategory;
    return matchesSearch && matchesCategory;
  });
}
}
