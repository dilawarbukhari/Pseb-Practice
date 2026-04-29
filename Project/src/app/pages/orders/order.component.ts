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

/**
 * Check if an order can be cancelled
 * Orders can only be cancelled if they are pending or in processing state
 */
isOrderCancelable(order: any): boolean {
  const cancelableStatuses = ['pending', 'processing', 'ordered'];
  return !cancelableStatuses.includes(order?.status_Name?.toLowerCase());
}

/**
 * Cancel an order
 */
cancelOrder(order: any): void {
  if (!order?.order_Id) {
    this._toasterService.warning('Invalid order', 'Warning');
    return;
  }

 this._userProductService.cancelOrder(order.order_Id).subscribe({
    next: (response: any) => {
      if(response[1].status !== 200){
      this._toasterService.error(response[0].message, 'Warning');
      }
      this._toasterService.success('Order cancelled successfully', 'Success');
      this.getAllOrders();
    },
    error: (error) => {
      if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
}


}
