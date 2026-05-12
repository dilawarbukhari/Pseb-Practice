import { Component, OnInit } from '@angular/core';
import { UserProductService } from '../../Service/userproduct.service';
import { ToastrService } from 'ngx-toastr';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';


@Component({
  selector: 'app-ordermanagement',
  imports: [CommonModule, FormsModule, NgxPaginationModule],
  templateUrl: './ordermanagement.component.html',
  styleUrl: './ordermanagement.component.css'
})
export class OrdermanagementComponent implements OnInit {
productResponseList:any=[];
PendingOrder:number=0;
ShippedOrder:number=0;
DeliveredOrder:number=0;
TotalOrder:number=0;
statusResponseList:any=[];
  p: number = 1;
filteredProductResponseList:any=[];
searchTerm = '';
statusOptions = [
  { value: 'Confirmed', label: 'Confirmed', color: 'info' },
  { value: 'Processing', label: 'Processing', color: 'warning' },
  { value: 'Shipped', label: 'Shipped', color: 'primary' },
  { value: 'Delivered', label: 'Delivered', color: 'success' },
  { value: 'Cancelled', label: 'Cancelled', color: 'danger' }
];

constructor(private _productService: UserProductService, private _toasterService: ToastrService) { }

ngOnInit(): void {
  this.getAllProduct();
  this.getAllStatus();
  this.getTotalrecord();
  // this.getPendingOrder();
  // this.getShippedOrder();
  // this.getDeliveredOrder();
}
  
getAllProduct(){
this._productService.getConfirmOrder().subscribe({
  next:(response:any)=>{
      this.productResponseList = (response.data || []).map((order: any) => ({
        ...order,
        selectedStatusId: order?.status_Id || order?.status
      }));
      this.filteredProductResponseList = [...this.productResponseList];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
getTotalrecord(){

this._productService.Totalrecord().subscribe({
    next:(response:any)=>{
        debugger
      this.PendingOrder = response.data[0].pending_orders;
      this.ShippedOrder=response.data[0].shipped_orders;
      this.TotalOrder=response.data[0].total_orders;
      this.DeliveredOrder=response.data[0].delivered_orders;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });

}
getAllStatus(){
  this._productService.getStatus().subscribe({
    next:(response:any)=>{
      this.statusResponseList = response.data;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
}



onSearchChange() {
  debugger
  this.p = 1;
  const term = this.searchTerm.trim().toLowerCase();
  if (!term) {
    this.filteredProductResponseList = [...this.productResponseList];
    return;
  }
  this.filteredProductResponseList = this.productResponseList.filter((order: any) =>
      order.order_number.toLowerCase().includes(term) ||
    order.product_name.toLowerCase().includes(term) ||
    order.Category_name.toLowerCase().includes(term) ||
    order.status_Name.toLowerCase().includes(term)

  );
}

updateStatus(product: any) {

  if (!product?.status_Id) {
    this._toasterService.warning('Please select a status first', 'Warning');
    return;
  }
  const payload={
    product_Id: product.product_id,
    order_Id: product.order_Id,
    status_Id: parseInt(product.selectedStatusId)
  }
  this._productService.updateOrderStatus(payload).subscribe({
    next: (response: any) => {
        if(response[1].status !== 200){
      this._toasterService.error(response[0].message, 'Warning');
        }
        this._toasterService.success(response[0].message, 'Success');
        // this.getPendingOrder();
        // this.getShippedOrder();
        // this.getDeliveredOrder();
      this.getTotalrecord();
        this.getAllProduct();
    },
    error: (error) => {
      this._toasterService.error(error.error?.response || 'Failed to update order status', 'Error');
    }
  });
}
getPendingOrder(){
  this._productService.getPendingOrders().subscribe({
    next:(response:any)=>{

      this.PendingOrder = response.data.pending_orders;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
}



getShippedOrder(){
 this._productService.getShippedOrder().subscribe({
    next:(response:any)=>{

      this.ShippedOrder = response.data.shipped_orders;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
}
getDeliveredOrder(){
 this._productService.getDeliveredOrder().subscribe({
    next:(response:any)=>{
      this.DeliveredOrder = response.data.delivered_orders;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
}
// Get status badge color
getStatusColor(status: string): string {
  const statusObj = this.statusOptions.find(s => s.value === status);
  return statusObj ? statusObj.color : 'secondary';
}

// Get status icon
getStatusIcon(status: string): string {

  switch ((status || '').toLowerCase()) {

    case 'confirmed':
      return 'patch-check-fill';

    case 'shipped':
      return 'box-seam-fill';

    case 'intransit':
    case 'in transit':
      return 'truck';

    case 'delivered':
      return 'check-circle-fill';

    case 'cancelled':
      return 'x-circle-fill';

    case 'pending':
      return 'clock-fill';

    default:
      return 'info-circle-fill';
  }
}
}
