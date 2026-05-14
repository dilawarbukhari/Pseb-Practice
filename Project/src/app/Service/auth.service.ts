import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
 HttpClientModule

@Injectable({
  providedIn: 'root'
})
export class AuthService {
   Url = 'http://localhost/MVC/route.php';

  constructor(private http : HttpClient) { }
  login(data:any):Observable<any>{
    const payload = {
    ...data,          // email & password
    process: 'loginUser'
  };
    return this.http.post<any>(this.Url, payload);
}
   register(data:any):Observable<any>{
    const payload = {
    ...data,  
    process: 'registerUser'
  };
    return this.http.post<any>(this.Url, payload);
}
  verifyOtp(data:string,user_Id:number):Observable<any>{
    const payload = {
    otpNumber:data , 
    user_Id:user_Id,
    process: 'verifyOtp'
  };
    return this.http.post<any>(this.Url, payload);
}
  ResendOTP(user_Id:any):Observable<any>{
    debugger
    const payload = { 
    user_Id:user_Id,
    process: 'ResendOTP'
  };
    return this.http.post<any>(this.Url, payload);
}
 changePassword(data:any):Observable<any>{
    const payload = {
    ...data,  
    process: 'changePassword'
  };
    return this.http.post<any>(this.Url, payload);
}
forgotPassword(data:any):Observable<any>{
    const payload = {
    ...data,  
    process: 'forgotPasswordOTP'
  };
    return this.http.post<any>(this.Url, payload);
}
  verifyforgetPasswordOtp(data:string,email:string):Observable<any>{
    debugger
    const payload = {
    otpNumber:data, 
    email:email,
    process: 'verifyforgetPasswordOtp'
  };
    return this.http.post<any>(this.Url, payload);
}
ForgotPassword(data:any,email:string):Observable<any>{
    const payload = {
    ...data,  
    email: email,
    process: 'ForgotPassword'
  };
    return this.http.post<any>(this.Url, payload);
}
ResendforgotOTP(email:string):Observable<any>{
    debugger
    const payload = { 
    email:email,
    process: 'ResendforgotOTP'
  };
    return this.http.post<any>(this.Url, payload);
}
}
