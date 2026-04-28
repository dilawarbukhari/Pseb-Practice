import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})

export class UserProductService {

Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
 orderPlace(data:any):Observable<any>{
 const payload ={
...data,
 process: 'orderPlace',
 }
return this._http.post<any>(this.Url,payload);
}
getAllOrders():Observable<any>{
    debugger
const process= 'getOrderDetail';
 return this._http.post<any>(this.Url, {process});
}
getConfirmOrder():Observable<any>{
const process= 'getConfirmOrder';
 return this._http.post<any>(this.Url, {process});
}
getStatus():Observable<any>{
const process= 'getStatus';
 return this._http.post<any>(this.Url, {process});
}
updateOrderStatus(data:any): Observable<any> {
const process = 'updateOrderStatus';
return this._http.post<any>(this.Url, { process, ...data });
}

getPendingOrders():Observable<any>{
const process= 'getPendingOrders';
 return this._http.post<any>(this.Url, {process});
}
getShippedOrder():Observable<any>{
const process= 'getShippedOrder';
 return this._http.post<any>(this.Url, {process});
}
getDeliveredOrder():Observable<any>{
const process= 'getDeliveredOrder';
 return this._http.post<any>(this.Url, {process});
}
}
