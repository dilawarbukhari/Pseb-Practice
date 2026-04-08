import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
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
registerForm! : FormGroup

public constructor(private _fb: FormBuilder,private _authService: AuthService, private _toastr: ToastrService,private _route : Router){

}
ngOnInit(){
this.setValidation();
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
    if(this.registerForm.valid){
      this._authService.register(this.registerForm.value).subscribe({
      next:(response:any) => {
        debugger
        if(response[1].status == "200"){
           this._toastr.success(response[0].message, 'success');
      this._route.navigateByUrl("/login");
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