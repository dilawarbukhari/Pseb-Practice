import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule, ActivatedRoute } from '@angular/router';
import { AuthService } from '../../Service/auth.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-register',
  imports: [ReactiveFormsModule,RouterModule,CommonModule],
  templateUrl: './register.component.html',
  styleUrl: './register.component.css',
    providers:[ToastrService]
})
export class RegisterComponent implements OnInit {
registerForm! : FormGroup;
sellerDetailsForm! : FormGroup;
userRole: string = 'buyer';
currentStep: number = 1;

public constructor(private _fb: FormBuilder,private _authService: AuthService, private _toastr: ToastrService,private _route : Router, private _activatedRoute: ActivatedRoute){

}
ngOnInit(){
this.setValidation();
this.SellerDetailsForm();
this.getUserRole();
}

getUserRole(){
  this._activatedRoute.queryParams.subscribe(params => {
    this.userRole = params['role'] || 'buyer';
    if(this.userRole === 'seller'){
      this.currentStep = 1;
    } else {
      this.currentStep = 2;
    }
  });
}

SellerDetailsForm(){
  this.sellerDetailsForm = this._fb.group({
    businessName: ['', [Validators.required]],
    cnic: ['', [Validators.required]],
    bankAccount: ['', [Validators.required]],
    shopAddress: ['', [Validators.required]],
    taxId: ['', [Validators.required]]
  });
}

setValidation(){
  this.registerForm= this._fb.group({
  firstname : (['',Validators.required]),
  lastname : (['',Validators.required]),
  email: ['',[ Validators.required, Validators.pattern('^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}$')]],
    password: ['', [Validators.required, Validators.minLength(8), Validators.pattern('^(?=.*[A-Za-z])(?=.*\\d)(?=.*[@$!%*#?&])[A-Za-z\\d@$!%*#?&]{8,}$')]],
  confirmPassword: ['', Validators.required]
}, { validators: this.passwordMatchValidator});
  
}

submitSellerDetails(){
  if(this.sellerDetailsForm.valid){
    // localStorage.setItem('sellerDetails', JSON.stringify(this.sellerDetailsForm.value));
    this.currentStep = 2;
  } else {
    this._toastr.error('Please fill in all required fields', 'Validation Error');
  }
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

onRegister(){
  debugger
  if(this.registerForm.valid){
    let registrationData: any = this.registerForm.value;
    
    if(this.userRole === 'seller'){
      const sellerDetails = this.sellerDetailsForm.value;
      if(sellerDetails){
        registrationData = {
          ...registrationData,
          ...sellerDetails,
          role: 4
        };
      }
    } else {
      registrationData.role = 3;
    }
    debugger
    this._authService.register(registrationData).subscribe({
      next:(response:any) => {
        debugger
        if(response[1].status == "200"){
           this._toastr.success(response[0].message, 'success'); 
           localStorage.setItem('user_Id',response[2].user_Id);
      this._route.navigateByUrl("/verify-email");
      }
      this._toastr.error(response[0].message, 'Warning');
      },

      error: (error:any)=>{
        this._toastr.error(error.error , 'error');
      }
    })
    }
    this.registerForm.markAllAsTouched();
  }
}