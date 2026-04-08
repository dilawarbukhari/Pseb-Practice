import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RoleService {

Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
 token= localStorage.getItem('accessToken')
addRole(role_name:string):Observable<any>{
  debugger  
 const payload ={
role_name,
 process: 'addRole',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}

deleteRole(id:number):Observable<any>{
  debugger
 const payload ={
  role_Id:id ,
  accessToken: this.token,
 process: 'deleteRole'
 }
return this._http.post<any>(this.Url,payload);
}
getRoles():Observable<any>{
  const payload ={
 process: 'getRoles',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
getRolePermission(id:number):Observable<any>{
  const payload ={
    role_Id : id,
 process: 'getRolePermission',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
updateRolePermission(data:any):Observable<any>{
  debugger
  const payload ={
    ...data,
 process: 'updateRolePermission',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
updateRole(data:any):Observable<any>{
  debugger
  const payload ={
    ...data,
 process: 'updateRole',
 accessToken: this.token
 }
return this._http.post<any>(this.Url,payload);
}
}

