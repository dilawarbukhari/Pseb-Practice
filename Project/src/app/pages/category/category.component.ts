import { Component, OnInit} from '@angular/core';
import { CategoryService } from '../../Service/category.service';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ToastrService } from 'ngx-toastr';
import { UpdateCategoryRequest } from '../../Interface/updateCategory';
import { elementAt } from 'rxjs';
import { NgxPaginationModule } from 'ngx-pagination';
import { CommonService } from '../../Service/common.service';


@Component({
  selector: 'app-category',
  imports: [ReactiveFormsModule,CommonModule,FormsModule,NgxPaginationModule],
  templateUrl: './category.component.html',
  styleUrl: './category.component.css'
})
export class CategoryComponent implements OnInit {
  categoryForm! :FormGroup;
  categoryResponseList:any =[];
  searchResponseList:any=[];
  searchText :string = '';
   p: number = 1;

  updateCategoryRequest :UpdateCategoryRequest;
  isEdit : boolean = true;
  category_id =0;
  constructor(private _categoryService:CategoryService, private _commonService:CommonService ,private _fb: FormBuilder,private _toasterService:ToastrService){
this.updateCategoryRequest= new UpdateCategoryRequest();
  }
  ngOnInit(): void {
    this.SetValidation();
    this.getAllCategories();
  }
SetValidation(){
this.categoryForm= this._fb.group({
  category_name: ['', Validators.required]
});
}
addButton(){
  this.isEdit=true;
  this.categoryForm.reset();
}
addCategory(){
  if(this.categoryForm.invalid){
   this.categoryForm.markAllAsTouched();
    return;
  }
this._categoryService.addCategory(this.categoryForm.value).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getAllCategories();
         this.categoryForm.reset();
        this._commonService.closeModal('categoryModal');
  },
  error:(error)=>{
     if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Not added');
  }
}
});
}
searchDetails(){
if(this.searchText ==''){
  this.categoryResponseList= this.searchResponseList;
  return;
}
 this.categoryResponseList= this.searchResponseList.filter((user: any) => {
     return user.category_name
        .toLowerCase()
        .includes(this.searchText.toLowerCase());
 })
}
getAllCategories(){

this._categoryService.getCategories().subscribe({
  next:(response:any)=>{
      this.categoryResponseList =response[0];
      this.searchResponseList=response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
deleteCategory(response:any){
 this.category_id = response.category_id
}
  confirmDelete(){
  this._categoryService.deleteCategory(this.category_id).subscribe({
    next:(response:any)=>{
      debugger
      if(response[1].status !== "200"){
      this._toasterService.error(response[0].message, 'Warning');
        }
        this._toasterService.success(response[0].message, 'Success');
          this.getAllCategories();
    },
    error:(error)=>{
  if(error.error?.response){
          this._toasterService.error(error.error.response, 'Error');
    } }
  })
  }
  updatedetails(response:any){
    this.isEdit=false;
    this.categoryForm.patchValue(response);
    this.category_id= response.category_id;
  }
 updateData(){
  this.updateCategoryRequest= this.categoryForm.value;
  this.updateCategoryRequest.category_id= this.category_id;

  this._categoryService.updateCategory(this.updateCategoryRequest).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getAllCategories();
         this.categoryForm.reset();
          this._commonService.closeModal('categoryModal');
  },
  error:(error)=>{
     if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Not added');
  }
}
});
 }
  }

