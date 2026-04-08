import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CategoryService {

    Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
 token= localStorage.getItem('accessToken')
addCategory(name:any):Observable<any>{
 const payload ={
...name,
 process: 'addCategory',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
deleteCategory(id:number):Observable<any>{
  debugger
 const payload ={
  category_id:id ,
  accessToken: this.token,
 process: 'deleteCategory'
 }
return this._http.post<any>(this.Url,payload);
}
getCategories():Observable<any>{
  const payload ={
 process: 'getAllCategory',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
updateCategory(data:any):Observable<any>{
  debugger
  const payload ={
    ...data,
 process: 'updateCategory',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
}
