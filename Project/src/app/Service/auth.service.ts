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
}
