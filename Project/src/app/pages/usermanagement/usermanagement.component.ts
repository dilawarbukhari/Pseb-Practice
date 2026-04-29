import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../Service/user.service';
import { RoleService } from '../../Service/role.service';
import { UpdateUserRequest } from '../../Interface/updateUser';
import { CommonService } from '../../Service/common.service';

@Component({
  selector: 'app-usermanagement',
  imports: [ReactiveFormsModule,CommonModule,FormsModule],
  templateUrl: './usermanagement.component.html',
  styleUrl: './usermanagement.component.css'
})
export class UsermanagementComponent {
userForm! :FormGroup;
  userResponseList:any=[];
  roleResponseList:any=[];
  searchResponseList:any=[];
  searchText :string = '';

updateUserRequest :UpdateUserRequest;
  isEdit : boolean = true;
  User_Id =0;
  constructor( private _userService:UserService,private _commonService:CommonService, private _roleService:RoleService, private _fb: FormBuilder,private _toasterService:ToastrService){
this.updateUserRequest= new UpdateUserRequest();
  }
  ngOnInit(): void {
    this.SetValidation();
    this.getAllUsers();
    this.getRoles();
  }
SetValidation() {
  this.userForm = this._fb.group({
    firstname: ['', Validators.required],
    lastname: ['', Validators.required],
    email: ['', [
      Validators.required,
      Validators.pattern("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$")
    ]],
    role_Id: ['', Validators.required]
  });
}
addButton(){
  this.isEdit=true;
  this.userForm.reset();
}
addUser(){
  if(this.userForm.invalid){
   this.userForm.markAllAsTouched();
    return;
  }
this._userService.addUser(this.userForm.value).subscribe({
  next:(response:any)=>{
    console.log(response);
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getAllUsers();
         this.userForm.reset();
         this._commonService.closeModal('userModal');
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
  this.userResponseList= this.searchResponseList;
  return;
}
 this.userResponseList= this.searchResponseList.filter((user: any) => {
     return user.firstname
        .toLowerCase()
        .includes(this.searchText.toLowerCase()) ||
        user.lastname
        .toLowerCase()
        .includes(this.searchText.toLowerCase()) ||  user.email.toLowerCase().includes(this.searchText.toLowerCase()) || user.role_name.toLowerCase().includes(this.searchText.toLowerCase()) ;
 })
 }
  getRoles(){
this._roleService.getRoles().subscribe({
  next:(response:any)=>{
    debugger
      this.roleResponseList =response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
 getAllUsers(){
this._userService.getUsers().subscribe({
  next:(response:any)=>{
      this.userResponseList =response[0];
      this.searchResponseList=response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
getAllCategories(){
// this._categoryService.getCategories().subscribe({
//   next:(response:any)=>{
//       this.categoryResponseList =response[0];
//   },
//   error:(error)=>{
// if(error.error?.response){
//         this._toasterService.error(error.error.response, 'Error');
//   } } })
}
deleteUser(response:any){
  debugger
 this.User_Id = response.Id
}
  confirmDelete(){
  this._userService.deleteUser(this.User_Id).subscribe({
    next:(response:any)=>{
      debugger
      if(response[1].status !== "200"){
      this._toasterService.error(response[0].message, 'Warning');
        }
        this._toasterService.success(response[0].message, 'Success');
          this.getAllUsers();
    },
    error:(error)=>{
  if(error.error?.response){
          this._toasterService.error(error.error.response, 'Error');
    } }
  })
  }
  updatedetails(response:any){
    this.isEdit=false;
    this.userForm.patchValue(response);
    this.User_Id= response.Id;
  }
 updateUser(){

  this.updateUserRequest= this.userForm.value;
  this.updateUserRequest.user_Id= this.User_Id;

  this._userService.updateUserDetail(this.updateUserRequest).subscribe({
  next:(response:any)=>{
 if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getAllUsers();
       this._commonService.closeModal('userModal');
  },
  error:(error)=>{
     if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Not added');
  }
}
});
  }}