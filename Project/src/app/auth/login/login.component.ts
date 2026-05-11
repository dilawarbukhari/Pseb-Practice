import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators, ɵInternalFormsSharedModule } from '@angular/forms';
import { AuthService } from '../../Service/auth.service';
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { Router, RouterLink, RouterModule } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { CommonService } from '../../Service/common.service';
import { UserService } from '../../Service/user.service';


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
 userResponseList:any=[];
 role='' ;
 user_Id: any ;
 rolename='' ;
 showRoleSelection = false;

  public constructor(private _commonService:CommonService,private _userService:UserService,private _fb: FormBuilder,private _authService : AuthService,private _toastr: ToastrService,private _router: Router){
  }
  ngOnInit(){
  this.setValidation();
  }
  
  onSignupClick(){
    this.showRoleSelection = true;
  }
  
  selectRole(role: string){
    this.showRoleSelection = false;
    this._router.navigate(['/register'], { queryParams: { role: role } });
  }
  
  closeRoleSelection(){
    this.showRoleSelection = false;
  }
setValidation(){
  this.loginForm = this._fb.group({
  email: ['',[ Validators.required, Validators.pattern('^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}$')]],
 password: ['', [Validators.required]],  
  });
}

getUserDetails(){
  const userId = this._commonService.getUserId();
 this._userService.getUser(userId!).subscribe({
  next:(response:any)=>{
   this.role= response[0][0].role_name.trim();
if(this.role === 'Admin'  ||  this.role == 'Super Admin'){
              this._router.navigateByUrl('/pages/dashboard');
    } else if(this.role === 'Buyer'){
        this._router.navigateByUrl('/pages/userproduct');
    }else{
      this._router.navigateByUrl('/pages/product');
    }
  },
  error:(error:any)=>{
    console.error('Error fetching user details:', error);
  }
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
      const data = response[2].data;
    
      debugger
          localStorage.setItem('accessToken', data[0].accessToken);
      const isChanged = this._commonService.getIsChanged();
      
      const isEmailVerifed = this._commonService.getIsEmail();
  
      if(isChanged === 0){
         localStorage.setItem('accessToken', data[0].accessToken);
           this._router.navigateByUrl('/forgotpassword');
        this._toastr.warning('Please change your password', 'Warning');
        return;
      }
      if(isEmailVerifed ===0){
        this._router.navigateByUrl('/verify-email');
         this._toastr.warning('Please verify your Email first', 'Warning');
        // const user_Id = this._commonService.getUserId;
         this.sendOTP();

        return;
      }
   
              this._toastr.success(response[0].message, 'Success');  
              this.getUserDetails();       
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



sendOTP(){

 this.user_Id= this._commonService.getUserId();
 localStorage.setItem('user_Id',this.user_Id);
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
  
}

