import { HttpInterceptorFn } from '@angular/common/http';
import { CommonService } from '../../Service/common.service';
import { inject } from '@angular/core';

export const authInterceptor: HttpInterceptorFn = (req, next) => {

    const tokenService = inject(CommonService); 
    const token = tokenService.getToken();
    if (token) {
        const clonedReq = req.clone({
            setHeaders: {
                Authorization: `Bearer ${token}`
            }
        });
        return next(clonedReq);
    }
  return next(req);
};
