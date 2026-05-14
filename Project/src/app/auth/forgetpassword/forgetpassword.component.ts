import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from '../../Service/auth.service';
import { CommonService } from '../../Service/common.service';

@Component({
  selector: 'app-forgetpassword',
  imports: [ReactiveFormsModule, CommonModule, RouterModule],
  templateUrl: './forgetpassword.component.html',
  styleUrl: './forgetpassword.component.css'
})
export class ForgetpasswordComponent implements OnInit {
  passwordForm!: FormGroup;
  resetPasswordForm! :FormGroup;
   secondsRemaining = 60;

   otpFrom!: FormGroup;
   otpNumber : string ='' ;
   user_Id :number= 0;
  private timerId?: ReturnType<typeof setInterval>;

  constructor(private _commonService :CommonService ,private fb: FormBuilder, private _toasterService: ToastrService,private _authService:AuthService,private _route:Router) { }

  ngOnInit() {
        this.startResendCountdown();
     this.SetValidation();
   this.setValidation();
   this.resetFormValidation();
  }
resetFormValidation(){
 this.resetPasswordForm = this.fb.group({
      newPassword: ['', [Validators.required]],
        confirmPassword: ['', Validators.required]
}, { validators: this.passwordMatchValidator});
 } ;


setValidation(){
 this.passwordForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });
}

 RegisteredEmail() {
    if (this.passwordForm.invalid) {
      this.passwordForm.markAllAsTouched();
      return;
    }
    this._authService.forgotPassword(this.passwordForm.value).subscribe({
      next:(response)=>{
        if(response[1].status == '200'){
          this._toasterService.success(response[0].message);
          this._commonService.openModal('verifyemailModal');
        }
       if (response[1].status == '400'){
          this._toasterService.warning(response[0].message);
       }
      },
      error:(error)=>{
           this._toasterService.warning(error,"Request failed");
      }
    })
  }


  ngOnDestroy(): void {
    this.clearResendCountdown();
  }

  SetValidation(){
    this.otpFrom = this.fb.group({
  d1: ['',Validators.required],
  d2: ['',Validators.required],
  d3: ['',Validators.required],
  d4: ['',Validators.required],
  d5: ['',Validators.required]
});
  }

generateOtp(){
  const otp= this.otpFrom.value.d1+ 
this.otpFrom.value.d2+this.otpFrom.value.d3+this.otpFrom.value.d4+this.otpFrom.value.d5

return otp;
}

  verifyOTP(){
if(!this.otpFrom.invalid){
    this.otpNumber = this.generateOtp();
  this._authService.verifyforgetPasswordOtp(this.otpNumber,this.passwordForm.get('email')?.value).subscribe({next:(response:any) =>
    {
     if(response[1].status == "200"){
           this._toasterService.success(response[0].message, 'success'); 
       this._commonService.openModal('resetPasswordModal');
      }
      this._toasterService.error(response[0].message, 'Warning');
      },
      error: (error:any)=>{
        this._toasterService.error(error.error , 'error');
      }
    })
    }
      }
 ResendOTP(){
  debugger
  this._authService.ResendforgotOTP(this.passwordForm.get('email')?.value).subscribe({next:(response:any) =>
    {
     if(response[1].status == "200"){
           this._toasterService.success(response[0].message, 'success'); 
      }
      this._toasterService.error(response[0].message, 'Warning');
      },
      error: (error:any)=>{
        this._toasterService.error(error.error , 'error');
      }
    })
    }   
    passwordMatchValidator(form: FormGroup) {
  const password = form.get('password');
  const confirm = form.get('confirmPassword');
  if (!password || !confirm) return null;
  if (confirm.value === '') return null; 
  if (password.value !== confirm.value) {
    confirm.setErrors({ mismatch: true });
  } else {
    if (confirm.hasError('mismatch')) {
      confirm.setErrors(null);
    }
  }
  return null; 
}
  savePassword(){
  if (this.resetPasswordForm.invalid){
    return this.resetPasswordForm.markAllAsTouched();
  }
  this._authService.ForgotPassword(this.resetPasswordForm.value,this.passwordForm.get('email')?.value).subscribe({
next:(response)=>{
  if(response[1].status == '200'){
    this._toasterService.success(response[0].message);
    this._commonService.closeModal('resetPasswordModal');
        this._commonService.closeModal('verifyemailModal');
    this._route.navigateByUrl('/login');
  }
  this._toasterService.warning(response[0].message);
},
error:(error:any) =>{
this._toasterService.warning(error,'Something went wrong');
}
  })
  }  
  startResendCountdown(): void {
    this.secondsRemaining = 60;
    this.clearResendCountdown();

    this.timerId = setInterval(() => {
      if (this.secondsRemaining > 0) {
        this.secondsRemaining--;
        return;
      }

      this.clearResendCountdown();
    }, 1000);
  }

  private clearResendCountdown(): void {
    if (this.timerId) {
      clearInterval(this.timerId);
      this.timerId = undefined;
    }
  }

}
