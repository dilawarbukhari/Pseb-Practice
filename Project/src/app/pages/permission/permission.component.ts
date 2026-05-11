import { Component,OnInit } from '@angular/core';
import { CategoryService } from '../../Service/category.service';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { CommonModule } from '@angular/common';
import { NgxPaginationModule } from 'ngx-pagination';
import { PermissionService } from '../../Service/permission.service';
import { CommonService } from '../../Service/common.service';

@Component({
  selector: 'app-permission',
  imports: [CommonModule,FormsModule,ReactiveFormsModule,NgxPaginationModule],
  templateUrl: './permission.component.html',
  styleUrl: './permission.component.css'
})
export class PermissionComponent implements OnInit {
permissionForm! :FormGroup;
  categoryResponseList:any =[];
  searchResponseList:any=[];
  permissionsResponseList:any =[];
  searchText :string = '';
  p: number = 1;

  isEdit : boolean = true;
  permission_Id =0;
  constructor(private _categoryService:CategoryService,private _commonService: CommonService,private _fb: FormBuilder,private _toasterService:ToastrService, private _permissionService: PermissionService) { }

  ngOnInit(): void {
    this.SetValidation();
    this.getPermission();
  }
SetValidation(){
this.permissionForm= this._fb.group({
  permission_name: ['', Validators.required]
});
}
addButton(){
  this.isEdit=true;
  this.permissionForm.reset();
}
addPermission(){
  if(this.permissionForm.invalid){
   this.permissionForm.markAllAsTouched();
    return;
  }
this._permissionService.addPermission(this.permissionForm.value).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getPermission()
         this.permissionForm.reset();
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
  this.permissionsResponseList= this.searchResponseList;
  return;
}
 this.permissionsResponseList= this.searchResponseList.filter((user: any) => {
     return user.permission_name
        .toLowerCase()
        .includes(this.searchText.toLowerCase());
 })
}
getPermission(){
this._permissionService.getPermission().subscribe({
  next:(response:any)=>{
      this.permissionsResponseList =response[0];
      this.searchResponseList =response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
deletePermission(response:any){
 this.permission_Id = response.permission_Id
}
  confirmDelete(){
  this._permissionService.deletePermission(this.permission_Id).subscribe({
    next:(response:any)=>{
      if(response[1].status !== "200"){
      this._toasterService.error(response[0].message, 'Warning');
        }
        this._toasterService.success(response[0].message, 'Success');
          this.getPermission();
    },
    error:(error)=>{
  if(error.error?.response){
          this._toasterService.error(error.error.response, 'Error');
    } }
  })
  }
  updatedetails(response:any){
    this.isEdit=false;
    this.permissionForm.patchValue(response);
    this.permission_Id= response.permission_Id;
  }
 updateData(){
  if(this.permissionForm.invalid){
   this.permissionForm.markAllAsTouched();
    return;
  }
  const updateddata= { ...this.permissionForm.value, permission_Id: this.permission_Id }
  this._permissionService.updatePermission(updateddata).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getPermission();
         this.permissionForm.reset();
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
