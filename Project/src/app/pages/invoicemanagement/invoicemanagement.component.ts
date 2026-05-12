import { Component } from '@angular/core';
import { SharedService } from '../../Service/shared.service';
import { ToastrService } from 'ngx-toastr';
import { CommonService } from '../../Service/common.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-invoicemanagement',
  imports: [FormsModule,CommonModule],
  templateUrl: './invoicemanagement.component.html',
  styleUrl: './invoicemanagement.component.css'
})
export class InvoicemanagementComponent {
SaleResponseList = '';
fromDate='';
toDate= '';
constructor(private _route:Router, private _sharedService: SharedService,private _toaster:ToastrService,private _commonService:CommonService){
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
generateWeeklySaleReport(){
this._sharedService.generateWeeklySaleReport().subscribe({
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
generateMonthlySaleReport(){
this._sharedService.generateMonthlySaleReport().subscribe({
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
generateYearlySaleReport(){
this._sharedService.generateYearlySaleReport().subscribe({
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
refresh(){
  this.fromDate='';
  this.toDate='';
}
downloadReport(){
  const dates={
  fromDate: this.fromDate,
  toDate: this.toDate,
   process: 'generateSpecificSaleReport'
  }
this._sharedService.generateSpecificSaleReport(dates).subscribe({
  next:(response) =>{
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

