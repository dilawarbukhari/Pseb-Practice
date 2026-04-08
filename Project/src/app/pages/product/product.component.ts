import { Component } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { ProductService } from '../../Service/product.service';
import { CategoryService } from '../../Service/category.service';
import { CommonModule } from '@angular/common';
import { UpdateProductRequest } from '../../Interface/updateProduct';

@Component({
  selector: 'app-product',
  imports: [ReactiveFormsModule,FormsModule,CommonModule],
  templateUrl: './product.component.html',
  styleUrl: './product.component.css'
})
export class ProductComponent {
 productForm! :FormGroup;
  categoryResponseList:any =[];
  productResponseList:any=[];
  searchResponseList:any=[];
  searchText :string = '';

  updateProductRequest :UpdateProductRequest;
  isEdit : boolean = true;
  product_Id =0;
  constructor(private _productService: ProductService, private _categoryService:CategoryService   ,private _fb: FormBuilder,private _toasterService:ToastrService){
this.updateProductRequest= new UpdateProductRequest();
  }
  ngOnInit(): void {
    this.SetValidation();
    this.getAllProduct();
     this.getAllCategories();
  }
SetValidation(){
   this.productForm = this._fb.group({
  product_name: ['', Validators.required],
  category_id: ['', Validators.required],
  price: ['', Validators.required],
  quantity: ['', Validators.required]
});
}
addButton(){
  this.isEdit=true;
  this.productForm.reset();
}
addProduct(){
  debugger
  if(this.productForm.invalid){
   this.productForm.markAllAsTouched();
    return;
  }
this._productService.addProduct(this.productForm.value).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getAllProduct();
         this.productForm.reset();
  },
  error:(error:any)=>{
     if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Not added');
  }
}
});
}
searchDetails(){
  debugger
if(this.searchText ==''){
  this.productResponseList= this.searchResponseList;
  return;
}
 this.productResponseList= this.searchResponseList.filter((user: any) => {
     return user.product_name
        .toLowerCase()
        .includes(this.searchText.toLowerCase()) ||
        user.category_name
        .toLowerCase()
        .includes(this.searchText.toLowerCase()) ||  user.price.includes(this.searchText) ;
 })
}
getAllProduct(){
  debugger
this._productService.getProduct().subscribe({
  next:(response:any)=>{
      this.productResponseList =response[0];
      this.searchResponseList=response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
getAllCategories(){
this._categoryService.getCategories().subscribe({
  next:(response:any)=>{
      this.categoryResponseList =response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
deleteProduct(response:any){
 this.product_Id = response.product_Id
}
  confirmDelete(){
    debugger
  this._productService.deleteProduct(this.product_Id).subscribe({
    next:(response:any)=>{
      debugger
      if(response[1].status !== "200"){
      this._toasterService.error(response[0].message, 'Warning');
        }
        this._toasterService.success(response[0].message, 'Success');
          this.getAllProduct();
    },
    error:(error)=>{
  if(error.error?.response){
          this._toasterService.error(error.error.response, 'Error');
    } }
  })
  }
  updatedetails(response:any){
    debugger
    this.isEdit=false;
    this.productForm.patchValue(response);
    this.product_Id= response.product_Id;
  }
 updateProduct(){
  debugger
  this.updateProductRequest= this.productForm.value;
  this.updateProductRequest.product_Id= this.product_Id;

  this._productService.updateProduct(this.updateProductRequest).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getAllProduct();
         this.productForm.reset();
  },
  error:(error)=>{
     if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Not added');
  }
}
});
  }
}
