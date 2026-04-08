import { Injectable } from '@angular/core';
import { jwtDecode } from 'jwt-decode';

@Injectable({
  providedIn: 'root'
})
export class CommonService {

  constructor() { }

  getToken():string | null {
    return localStorage.getItem('accessToken');
  }


  getUserId(): number | null {
    debugger
    const getToken= this.getToken();
    if(!getToken){
      return null;
    }
    const decode:any = jwtDecode(getToken);
    return decode.$userId || null;
  }
}
