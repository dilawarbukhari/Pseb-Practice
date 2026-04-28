import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DashboardService {

    Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }

getTotalRevenue():Observable<any>{
 const process = 'getTotalRevenue';
 return this._http.post<any>(this.Url,{process});
 }
   getRecentOrder():Observable<any>{
 const process = 'getRecentOrder';
 return this._http.post<any>(this.Url,{process});
 }
  getThisMonth():Observable<any>{
 const process = 'getThisMonth';
 return this._http.post<any>(this.Url,{process});
 }
getTotalUser():Observable<any>{
 const process = 'getTotalUser';
 return this._http.post<any>(this.Url,{process});
 }
getActiveUsers():Observable<any>{
 const process = 'getActiveUsers';
 return this._http.post<any>(this.Url,{process});
 }

getTotalProduct():Observable<any>{
 const process = 'getTotalProduct';
 return this._http.post<any>(this.Url,{process});
 }

getTotalOrder():Observable<any>{
 const process = 'getTotalOrder';
 return this._http.post<any>(this.Url,{process});
 }

getTotalCategory():Observable<any>{
 const process = 'getTotalCategory';
 return this._http.post<any>(this.Url,{process});
 }
}
