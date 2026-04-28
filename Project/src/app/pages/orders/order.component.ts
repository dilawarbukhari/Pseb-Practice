import { Component,OnInit } from '@angular/core';
import { UserProductService } from '../../Service/userproduct.service';
import { ToastrService } from 'ngx-toastr';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-order',
  imports: [CommonModule],
  templateUrl: './order.component.html',
  styleUrl: './order.component.css'
})
export class OrderComponent implements OnInit{
 orderResponseList:any=[];

  constructor(private _userProductService: UserProductService,private _toasterService: ToastrService) { }

  ngOnInit(): void {
    this.getAllOrders();
  }


getAllOrders(){
  debugger
  this._userProductService.getAllOrders().subscribe({
    next:(response:any)=>{
      debugger
     this.orderResponseList=response.data;

    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  })
}

}
