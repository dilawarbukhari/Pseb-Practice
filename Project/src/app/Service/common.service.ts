import { Injectable } from '@angular/core';
import { jwtDecode } from 'jwt-decode';
declare var bootstrap: any;
@Injectable({
  providedIn: 'root'
})
export class CommonService {

  constructor() { }

  getToken():string | null {
    return localStorage.getItem('accessToken');
  }
 closeModal(modalId: string): void {
  const modalElement = document.getElementById(modalId);

  if (modalElement) {
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.hide();
  }

  // 🔥 FIX: Remove leftover backdrop manually
  setTimeout(() => {
    document.body.classList.remove('modal-open');

    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(el => el.remove());
  }, 300); // wait for animation
}

  getUserId(): number | null {
    const getToken= this.getToken();
    if(!getToken){
      return null;
    }
    const decode:any = jwtDecode(getToken);
    return decode.$userId || null;
  }
}
