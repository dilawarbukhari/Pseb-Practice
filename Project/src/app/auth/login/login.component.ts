import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators, ɵInternalFormsSharedModule } from '@angular/forms';
import { AuthService } from '../../Service/auth.service';
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ToastrService } from 'ngx-toastr';


@Component({
  selector: 'app-login',
  imports: [ReactiveFormsModule, HttpClientModule, CommonModule, RouterModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css',
  providers:[ToastrService]
})
export class LoginComponent implements OnInit {
 loginForm! : FormGroup;
 token = '';

  public constructor(private _fb: FormBuilder,private _authService : AuthService,private _toastr: ToastrService){
  }
  ngOnInit(){
  this.setValidation();
  }
setValidation(){
  this.loginForm = this._fb.group({
  email: ['',[ Validators.required, Validators.pattern('^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}$')]],
 password: ['', [Validators.required, Validators.minLength(8), Validators.pattern('^(?=.*[A-Za-z])(?=.*\\d)(?=.*[@$!%*#?&])[A-Za-z\\d@$!%*#?&]{8,}$')]],  
  });
}
onLogin(){

if(this.loginForm.valid){
this._authService.login(this.loginForm.value).subscribe({      
    next: (response: any) => {
     debugger
      if(response[1].status !== "200"){
     this._toastr.error(response[0].message, 'Warning');
      }
      const token = response[2].data;
    localStorage.setItem('accessToken', token[0].accessToken);
        localStorage.setItem('refreshToken', token[1].refreshToken);
              this._toastr.success(response[0].message, 'Success');
              // this.loginForm.reset();
      //  localStorage.setItem('_token', token);
    },
    error: (error) => {
      if (error.error?.response) {
        this._toastr.error(error.error.response, 'Login Failed');
      } else {
        this._toastr.error('Something went wrong', 'Error');
      }
    }
  });
}
this.loginForm.markAllAsTouched();
}
}
