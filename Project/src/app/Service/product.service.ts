import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ProductService {

    Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
 token= localStorage.getItem('accessToken')
addProduct(data:any):Observable<any>{
 const payload ={
...data,
 process: 'addProduct',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
deleteProduct(id:number):Observable<any>{
  debugger
 const payload ={
  product_Id:id ,
  accessToken: this.token,
 process: 'deleteProduct'
 }
return this._http.post<any>(this.Url,payload);
}
getProduct():Observable<any>{
  const payload ={
 process: 'getAllProduct',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
updateProduct(data:any):Observable<any>{
  debugger
  const payload ={
    ...data,
 process: 'updateProduct',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
}
