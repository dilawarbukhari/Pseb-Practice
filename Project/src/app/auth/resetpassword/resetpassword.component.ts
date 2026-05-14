import { CommonModule } from '@angular/common';
import { Component,OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonService } from '../../Service/common.service';
import { UserService } from '../../Service/user.service';
import { AuthService } from '../../Service/auth.service';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-resetpassword',
  imports: [ReactiveFormsModule,CommonModule,FormsModule],
  templateUrl: './resetpassword.component.html',
  styleUrl: './resetpassword.component.css'
})
export class ResetpasswordComponent implements OnInit {
passwordForm!: FormGroup;
userResponseList:any=[];
  constructor(private fb:FormBuilder,private commonService:CommonService,private userService:UserService,private authService:AuthService,private _route:Router,private _toasterService:ToastrService) { }

  ngOnInit() {
    this.setPasswordValidation();
    
    this.getUserDetails();
  }
  isTokenPresent(): boolean {
    debugger
  return !!localStorage.getItem('accessToken');
}
  setPasswordValidation() {
    this.passwordForm = this.fb.group({
    email: ['', [Validators.required, Validators.email]],   
    oldPassword: ['', Validators.required],
    newPassword: [
      '',
      [
        Validators.required,
        Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/)
      ]
    ] ,
    confirmPassword: ['', Validators.required]
  }, { validators: this.passwordMatchValidator });

    if (!!localStorage.getItem('accessToken')) {
    this.passwordForm.get('email')?.disable();  
  }
  }
  passwordMatchValidator(form: any) {
  const pass = form.get('newPassword')?.value;
  const confirm = form.get('confirmPassword')?.value;

  return pass === confirm ? null : { passwordMismatch: true };
}
getUserDetails(){
  
  const userId = this.commonService.getUserId();
 this.userService.getUser(userId!).subscribe({
  next:(response:any)=>{
   this.userResponseList=response[0];
   this.passwordForm.patchValue({
    email:this.userResponseList[0].email
   })
  },
  error:(error:any)=>{
    console.error('Error fetching user details:', error);
  }
 });
}
updatePassword() {
  debugger
if (this.passwordForm.invalid) {
  return this.passwordForm.markAllAsTouched();
}
const formData = this.passwordForm.getRawValue();
this.authService.changePassword(formData).subscribe({
  next: (response) => {
     if(response[1].status !== "200"){
     this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success(response[0].message, 'Success');
    console.log('Password changed successfully:', response);
    this._route.navigateByUrl('/login');
    this.passwordForm.reset();

  },
  error: (error) => {
  this._toasterService.error('Failed to change password. Please try again.', 'Warning');
  }
})
}
}

