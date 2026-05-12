import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from "@angular/forms";
import { AuthService } from '../../Service/auth.service';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-verify-email',
  imports: [FormsModule,ReactiveFormsModule,CommonModule],
  templateUrl: './verify-email.component.html',
  styleUrl: './verify-email.component.css'
})
export class VerifyEmailComponent implements OnInit, OnDestroy {
  secondsRemaining = 60;

   otpFrom!: FormGroup;
   otpNumber : string ='' ;
   user_Id :number= 0;
  private timerId?: ReturnType<typeof setInterval>;


  constructor(private _fb:FormBuilder,private _authService:AuthService,private _route:Router,private _toastr: ToastrService){

  }
  ngOnInit(): void {
    this.startResendCountdown();
     this.SetValidation();
  }

  ngOnDestroy(): void {
    this.clearResendCountdown();
  }

  SetValidation(){
    this.otpFrom = this._fb.group({
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
   this.user_Id = Number(localStorage.getItem('user_Id'));
  this._authService.verifyOtp(this.otpNumber,this.user_Id).subscribe({next:(response:any) =>
    {
     if(response[1].status == "200"){
           this._toastr.success(response[0].message, 'success'); 
      this._route.navigateByUrl("/login");
      localStorage.removeItem('user_Id');
      }
      this._toastr.error(response[0].message, 'Warning');
      },

      error: (error:any)=>{
        this._toastr.error(error.error , 'error');
      }
    })
    }
      }
 ResendOTP(){
  debugger
   this.user_Id = Number(localStorage.getItem('user_Id'));
  this._authService.ResendOTP(this.user_Id).subscribe({next:(response:any) =>
    {
     if(response[1].status == "200"){
           this._toastr.success(response[0].message, 'success'); 
      }
      this._toastr.error(response[0].message, 'Warning');
      },
      error: (error:any)=>{
        this._toastr.error(error.error , 'error');
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
