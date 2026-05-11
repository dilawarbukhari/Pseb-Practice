import { Component } from '@angular/core';
import { SharedService } from '../../Service/shared.service';
import { ToastrService } from 'ngx-toastr';
import { CommonService } from '../../Service/common.service';

@Component({
  selector: 'app-invoicemanagement',
  imports: [],
  templateUrl: './invoicemanagement.component.html',
  styleUrl: './invoicemanagement.component.css'
})
export class InvoicemanagementComponent {
SaleResponseList = '';
constructor(private _sharedService: SharedService,private _toaster:ToastrService,private _commonService:CommonService){
}
generateSaleReport(){
this._sharedService.generateSaleReport().subscribe({
  next:(response) =>{
    debugger
    if(response.status == "200"){
  this.SaleResponseList = response.pdfBase64;
  this._commonService.getPdf(this.SaleResponseList);
    }
  },
  error:(error)=>{
    this._toaster.warning('error', "Something went wrong");
    
  }
})
}
}
