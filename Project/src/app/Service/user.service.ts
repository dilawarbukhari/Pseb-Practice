import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {

Url = 'http://localhost/MVC/route.php';
  constructor(private _http : HttpClient) { 
  }
 token= localStorage.getItem('accessToken')
addUser(data:any):Observable<any>{
 const payload ={
...data,
 process: 'addUser',

 }
return this._http.post<any>(this.Url,payload);
}
getUser(userId:number):Observable<any>{
 const payload ={
user_Id:userId  ,
 process: 'getUserDetail',

 }
return this._http.post<any>(this.Url,payload);
}
deleteUser(id:number):Observable<any>{
  debugger
 const payload ={
  user_Id:id ,

 process: 'deleteUser'
 }
return this._http.post<any>(this.Url,payload);
}
getUsers():Observable<any>{
  const payload ={
 process: 'getAllUser',
 }
return this._http.post<any>(this.Url,payload);
}
changePassword(data:any):Observable<any>{
 debugger
  const payload ={
    ...data,
 process: 'changePassword',
 }
return this._http.post<any>(this.Url,payload);
}
updateUserDetail(data:any):Observable<any>{
 debugger
  const payload ={
    ...data,
 process: 'updateUserDetail',
 }
return this._http.post<any>(this.Url,payload);
}
updateUser(data:any):Observable<any>{
 debugger
  const payload ={
    ...data,
 process: 'updateUser',
 }
return this._http.post<any>(this.Url,payload);
}
}

