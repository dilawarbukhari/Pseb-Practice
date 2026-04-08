import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators, ɵInternalFormsSharedModule } from '@angular/forms';
import { AuthService } from '../../Service/auth.service';
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { Router, RouterLink, RouterModule } from '@angular/router';
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

  public constructor(private _fb: FormBuilder,private _authService : AuthService,private _toastr: ToastrService,private _router: Router){
  }
  ngOnInit(){
  this.setValidation();
  }
setValidation(){
  this.loginForm = this._fb.group({
  email: ['',[ Validators.required, Validators.pattern('^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}$')]],
 password: ['', [Validators.required]],  
  });
}
onLogin(){

if(this.loginForm.valid){
this._authService.login(this.loginForm.value).subscribe({      
    next: (response: any) => {
   console.log(response);
      if(response[1].status !== "200"){
     this._toastr.error(response[0].message, 'Warning');
      }
         debugger
      const data = response[2].data;
      const isChanged = data[1].isChanged;
    
      if(isChanged === 0){
         localStorage.setItem('accessToken', data[0].accessToken);
           this._router.navigateByUrl('/forgotpassword');
        this._toastr.warning('Please change your password', 'Warning');
      
        return;
      }
    localStorage.setItem('accessToken', data[0].accessToken);
              this._toastr.success(response[0].message, 'Success');
              this._router.navigateByUrl('/pages/dashboard');
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
