import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContentComponent } from '../../content/content.component';
import { ProductService } from '../../Service/product.service';
import { UserService } from '../../Service/user.service';
import { CategoryService } from '../../Service/category.service';
import { DashboardService } from '../../Service/dashboard.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-dashboard',
  imports: [CommonModule],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent implements OnInit {
  // Statistics
  totalUsers: number = 0;
  totalProducts: number = 0;
  totalOrders: number = 0;
  totalRevenue: number = 0;
  totalCategories: number = 0;
  activeUsers: number = 0;
  newUsersMonth: number = 0;

  // Growth percentages
  userGrowth: number = 0;
  productGrowth: number = 0;
  orderGrowth: number = 0;
  revenueGrowth: number = 0;

  // Data arrays
  recentOrders: any[] = [];
  topProducts: any[] = [];
  topCategories: any[] = [];

  constructor(
    private _toasterService: ToastrService, 
    private _dashboardService: DashboardService,
    private productService: ProductService,
    private userService: UserService,
    private categoryService: CategoryService
  ) {}

  ngOnInit(): void {
    // this.loadDashboardData();
    this.getTotalRevenue();
    this.getTotalUser();
    this.getTotalProduct();
    this.getTotalCategory();
    this.getTotalOrder();
    this.getActiveUsers();
    this.getThisMonth();
    this.getRecentOrder();
    this.getTopProduct();
    this.getCategoryProduct();
  }
  getCategoryProduct(){
     this._dashboardService.getCategoryProduct().subscribe({
    next:(response:any)=>{
   debugger
      this.topCategories = response.data;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
  getTopProduct(){
     this._dashboardService.getTopProduct().subscribe({
    next:(response:any)=>{
      this.topProducts = response.data;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
  getRecentOrder(){
     this._dashboardService.getRecentOrder().subscribe({
    next:(response:any)=>{

      this.recentOrders = response.data;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }

  getTotalUser(){
    this._dashboardService.getTotalUser().subscribe({
    next:(response:any)=>{

      this.totalUsers = response.data.total_users;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
  getTotalProduct(){
    this._dashboardService.getTotalProduct().subscribe({
    next:(response:any)=>{
 
      this.totalProducts = response.data.total_products;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
   getThisMonth(){
     this._dashboardService.getThisMonth().subscribe({
    next:(response:any)=>{
      this.newUsersMonth = response.data.new_users;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
  getActiveUsers(){
     this._dashboardService.getActiveUsers().subscribe({
    next:(response:any)=>{
      this.activeUsers = response.data.active_users;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
    getTotalCategory(){
    this._dashboardService.getTotalCategory().subscribe({
    next:(response:any)=>{
  
      this.totalCategories = response.data.total_category;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
  getTotalRevenue(){
    this._dashboardService.getTotalRevenue().subscribe({
    next:(response:any)=>{

      this.totalRevenue = response.data.total_revenue;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
   getTotalOrder(){
    this._dashboardService.getTotalOrder().subscribe({
    next:(response:any)=>{

      this.totalOrders = response.data.total_orders;
    },
    error:(error)=>{
      if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  });
  }
 
}
