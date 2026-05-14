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

  setTimeout(() => {
    document.body.classList.remove('modal-open');

    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(el => el.remove());
  }, 300);
}
openModal(modalId: string): void {
  const modalElement = document.getElementById(modalId);
  if (modalElement) {
    const modal =
      bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
  }
  setTimeout(() => {
    document.body.classList.add('modal-open');

    const backdrops = document.querySelectorAll('.modal-backdrop');
  if (backdrops.length > 1) {

      backdrops.forEach((el, index) => {

        if (index !== backdrops.length - 1) {
          el.remove();
        }

      });
    }

  }, 300);
}

 getIsChanged():number | null  {
    const getToken= this.getToken();
    if(!getToken){
          return null;
    }
    const decode:any = jwtDecode(getToken);
    return decode.isChanged ?? null;
  }
 getIsEmail():number | null  {
  debugger
    const getToken= this.getToken();
    if(!getToken){
          return null;
    }
    const decode:any = jwtDecode(getToken);
    return decode.isEmailVerified ?? null;
  }
  getUserId(): number | null {
    debugger
    const getToken= this.getToken();
    if(!getToken){
      return null;
    }
    const decode:any = jwtDecode(getToken);
    return decode.$userId ?? null;
  }
 getPdf(pdfFile: string) {

  const byteCharacters = atob(pdfFile);

  const byteNumbers = new Array(byteCharacters.length);

  for (let i = 0; i < byteCharacters.length; i++) {
    byteNumbers[i] = byteCharacters.charCodeAt(i);
  }

  const byteArray = new Uint8Array(byteNumbers);

  const blob = new Blob(
    [byteArray],
    { type: 'application/pdf' }
  );

  const url = window.URL.createObjectURL(blob);

  window.open(url, '_blank');

}
}
