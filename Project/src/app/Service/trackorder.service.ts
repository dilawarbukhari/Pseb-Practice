import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TrackorderService {
Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
trackOrder(TrackingNumber:string):Observable<any>{
  debugger  
 const payload ={
trackingNumber: TrackingNumber,
 process: 'trackOrder'
 }
return this._http.post<any>(this.Url,payload);
}
}
