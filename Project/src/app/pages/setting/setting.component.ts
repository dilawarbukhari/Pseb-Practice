import { CommonModule } from '@angular/common';
import { Component,OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonService } from '../../Service/common.service';
import { UserService } from '../../Service/user.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-setting',
  imports: [CommonModule,ReactiveFormsModule],
  templateUrl: './setting.component.html',
  styleUrl: './setting.component.css'
})
export class SettingComponent implements OnInit {
profileForm!: FormGroup;
passwordForm!: FormGroup;
userResponseList:any=[];
userId= 0;

isEdit = false;
showPassword = false;

constructor(private fb: FormBuilder,private commonService: CommonService, private userService: UserService,private _toasterService:ToastrService) {}
ngOnInit(): void {
  this.SetValidation();
  this.getUserDetails();
this.setPasswordValidation()
}
SetValidation() {
 this.profileForm = this.fb.group({
    firstname: ['', Validators.required],
    lastname: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]]
  });
}
setPasswordValidation() {
  this.passwordForm = this.fb.group({
  newPassword: [
    '',
    [
      Validators.required,
      Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/)
    ]
  ] ,
  confirmPassword: ['', Validators.required]
}, { validators: this.passwordMatchValidator });
}
passwordMatchValidator(form: any) {
  const pass = form.get('newPassword')?.value;
  const confirm = form.get('confirmPassword')?.value;

  return pass === confirm ? null : { passwordMismatch: true };
}
updatePassword(){
  debugger
  if (!this.passwordForm.valid) {
    return this.passwordForm.markAllAsTouched();
  }

  this.userService.changePassword(this.passwordForm.value).subscribe({
    next: (response) => {
      if(response[1].status !== "200"){
      this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
      this.passwordForm.reset();
    },
    error: (error) => {
      console.error('Error changing password:', error);
    }
  });
}
getUserDetails(){
  
  const userId = this.commonService.getUserId();
 this.userService.getUser(userId!).subscribe({
  next:(response:any)=>{

   this.userResponseList=response[0];
  },
  error:(error:any)=>{
    console.error('Error fetching user details:', error);
  }
 });
}
isUpdate(user: any){
  this.profileForm.patchValue(user);

  this.userId = user.Id;
}
saveProfile(){
  if (!this.profileForm.valid) {

    return this.profileForm.markAllAsTouched();
  }
    const updatedData = { ...this.profileForm.value, user_Id: this.userId };
    this.userService.updateUserDetail(updatedData).subscribe({
      next: (response) => {
       if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
         this.getUserDetails();
         this.profileForm.reset();
  },
      error: (error) => {
        console.error('Error updating profile:', error);
      }
    });
  }
}



