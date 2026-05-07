import { Component } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../../Service/user.service';
import { ProductService } from '../../Service/product.service';
import { CategoryService } from '../../Service/category.service';
import { UpdateProductRequest } from '../../Interface/updateProduct';
import { ToastrService } from 'ngx-toastr';
import { CommonService } from '../../Service/common.service';
import { CommonModule } from '@angular/common';
import { NgxPaginationModule } from 'ngx-pagination';

@Component({
  selector: 'app-productdetail',
  imports: [FormsModule,CommonModule,ReactiveFormsModule,NgxPaginationModule],
  templateUrl: './productdetail.component.html',
  styleUrl: './productdetail.component.css'
})
export class ProductdetailComponent {
 imagePreview: string | ArrayBuffer | null = null;
  isUploading = false;
  loading = true;
   p: number = 1;
   userResponseList:any=[];
   role:string='';
fileName: string = '';
 productForm! :FormGroup;
 SelectedFile: File | null = null;
  categoryResponseList:any =[];
  productResponseList:any=[];
  searchResponseList:any=[];
  searchText :string = '';

  updateProductRequest :UpdateProductRequest;
  isEdit : boolean = true;
  product_Id =0;
  constructor(private userService:UserService ,private _productService: ProductService, private _categoryService:CategoryService   ,private _fb: FormBuilder,private _toasterService:ToastrService,private _commonService:CommonService){
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
  image: ['', Validators.required],
  price: ['', Validators.required],
  oldPrice: ['',Validators.required],
  quantity: ['', Validators.required],
  description:['', Validators.required]
});
}
addButton(){
  this.isEdit=true;
  this.productForm.reset();
  this.fileName='';
  this.SelectedFile = null;
  this.imagePreview = null;
}
// addProduct(){
//   if(this.productForm.invalid){
//    this.productForm.markAllAsTouched();
//     return;
//   }
//   const formdata = new FormData();
// formdata.append('product_name', this.productForm.get('product_name')?.value);
// formdata.append('category_id', this.productForm.get('category_id')?.value);
// formdata.append('price', this.productForm.get('price')?.value);
// formdata.append('oldPrice', this.productForm.get('oldPrice')?.value);
// formdata.append('quantity', this.productForm.get('quantity')?.value);
// formdata.append('image', this.productForm.get('image')?.value);
// formdata.append('description', this.productForm.get('description')?.value);
// formdata.append('process', 'addProduct');
// // if (this.fileName) {
// //   formdata.append('image', this.fileName);
// // }
// this._productService.addProduct(formdata).subscribe({
//   next:(response:any)=>{
//  if(response[1].status !== "200"){
//      this._toasterService.error(response[0].message, 'Warning');
//       }
//       this._toasterService.success(response[0].message, 'Success');
//          this.getAllProduct();
//           this.fileName='';
//           this.SelectedFile = null;
//           this.imagePreview = null;
//          this.productForm.reset();
//          this._commonService.closeModal('productModal');
//   },
//   error:(error:any)=>{
//      if (error.error?.response) {
//         this._toasterService.error(error.error.response, 'Not added');
//   }
// }
// });
// }
searchDetails(){
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

// onFileSelected(event: any) {
//   debugger
//   const file = event.target.files[0];

//   if (!file) return;

//   this.SelectedFile = file;
//   this.fileName = file.name;
//   this.isUploading = true;
//    const reader = new FileReader();
//     reader.onload = () => {
//       this.imagePreview = reader.result as string;
//     };
//     reader.readAsDataURL(file);

//   // Simulate upload (replace with API call)
//   setTimeout(() => {
//     this.isUploading = false;
//   }, 2000);
//   this.productForm.get('image')?.setValue(file);
//   this.productForm.get('image')?.markAsTouched();
//   this.productForm.get('image')?.updateValueAndValidity();
// }

getUserDetails(){
  const userId = this._commonService.getUserId();
 this.userService.getUser(userId!).subscribe({
  next:(response:any)=>{
   this.userResponseList=response[0];
   this.role=this.userResponseList[0].role_name.trim();
  },
  error:(error:any)=>{
    console.error('Error fetching user details:', error);
  }
 });
}

getAllProduct(){

this._productService.getProduct().subscribe({
  next:(response:any)=>{
    this.loading = false;
      // debugger
      this.productResponseList =response[0];
      this.searchResponseList=response[0];
      // Initialize tooltips after data is loaded
      // this.initializeTooltips();
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
//   updatedetails(response:any){ 
//     debugger
//     this.isEdit=false;
//         this.imagePreview = response.image;
//         this.productForm.patchValue({
//           product_name: response.product_name,
//           category_id: response.category_id,
//           price: response.price,
//           oldPrice: response.oldPrice,
//           description:response.description,
//           quantity: response.quantity
//         });
//         this.product_Id= response.product_Id;
//   }
//  updateProduct(){
//    const formdata = new FormData();
// formdata.append('product_name', this.productForm.get('product_name')?.value);
// formdata.append('category_id', this.productForm.get('category_id')?.value);
// formdata.append('price', this.productForm.get('price')?.value);
// formdata.append('oldPrice', this.productForm.get('oldPrice')?.value);
// formdata.append('quantity', this.productForm.get('quantity')?.value);
// formdata.append('image', this.productForm.get('image')?.value);
// formdata.append('description', this.productForm.get('description')?.value);
// formdata.append('process', 'updateProduct');
// formdata.append('product_Id',String(this.product_Id));

//   this._productService.updateProduct(formdata).subscribe({
//   next:(response:any)=>{
//  if(response[1].status !== "200"){
//      this._toasterService.error(response[0].message, 'Warning');
//       }
//       this._toasterService.success(response[0].message, 'Success');
//          this.getAllProduct();
//          this.fileName='';
//          this.SelectedFile = null;
//          this.imagePreview = null;
//          this.productForm.reset();
//        this._commonService.closeModal('productModal');
//   },
//   error:(error)=>{
//      if (error.error?.response) {
//         this._toasterService.error(error.error.response, 'Not added');
//   }
// }
// });
//   }

  /**
   * Truncate text to a specified number of words
   * @param text - The text to truncate
   * @param wordLimit - Number of words to show (default: 4)
   * @returns Truncated text with ellipsis if exceeds word limit
   */
  truncateText(text: string, wordLimit: number = 4): string {
    if (!text) return '';
    const words = text.trim().split(/\s+/);
    if (words.length > wordLimit) {
      return words.slice(0, wordLimit).join(' ') + '...';
    }
    return text;
  }

  /**
   * Get full text for tooltip
   * @param text - The full text
   * @returns Full text for displaying in tooltip
   */
  getFullText(text: string): string {
    return text || '';
  }

  /**
   * Initialize Bootstrap tooltips for description cells
   */
  // initializeTooltips(): void {
  //   setTimeout(() => {
  //     // Get all tooltip elements
  //     const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  //     tooltipElements.forEach((element: any) => {
  //       // Destroy existing tooltip if any
  //       const existingTooltip = bootstrap.Tooltip.getInstance(element);
  //       if (existingTooltip) {
  //         existingTooltip.dispose();
  //       }
  //       // Create new tooltip
  //       new bootstrap.Tooltip(element, {
  //         delay: { show: 200, hide: 100 }
  //       });
  //     });
  //   }, 100);
  // }

  /**
   * Close the product modal
   */

}
