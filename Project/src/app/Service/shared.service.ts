import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SharedService {
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
Feedback(data:any):Observable<any>{
return this._http.post<any>(this.Url,data);
}
getReviewStatus(data:any):Observable<any>{
return this._http.post<any>(this.Url,data);
}
generateSaleReport():Observable<any>{
  const data={
    process : 'generateSaleReport'
  }
return this._http.post<any>(this.Url,data);
}
}
