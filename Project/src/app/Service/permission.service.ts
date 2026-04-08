import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PermissionService {
Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
 token= localStorage.getItem('accessToken')
  getPermission():Observable<any>{
    const payload ={
   process: 'getAllPermission',
  //  accessToken: this.token
   }
  return this._http.post<any>(this.Url,payload);
  }
   addPermission(data:any):Observable<any>{
    debugger
    const payload ={
      ...data,
   process: 'addPermission',
  //  accessToken: this.token
   }
  return this._http.post<any>(this.Url,payload);
  }
  deletePermission(id:number):Observable<any>{
    const payload ={
     permission_Id: id,
   process: 'deletePermission',
  //  accessToken: this.token
   }
  return this._http.post<any>(this.Url,payload);
  }
  updatePermission(data:any):Observable<any>{
    const payload ={
      ...data,
   process: 'updatePermission',
  //  accessToken: this.token
   }
  return this._http.post<any>(this.Url,payload);
  }
}
