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
}
