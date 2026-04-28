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
  imports: [ContentComponent, CommonModule],
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
    this.loadDashboardData();
    this.getTotalUser();
    this.getTotalProduct();
    this.getTotalCategory();
    this.getTotalOrder();
    this.getActiveUsers();
    this.getThisMonth();
    this.getRecentOrder();
  }
  getRecentOrder(){
     this._dashboardService.getRecentOrder().subscribe({
    next:(response:any)=>{
   debugger
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
  loadDashboardData(): void {
    // Load products
    this.productService.getProduct().subscribe({
      next: (response: any) => {
        if (response.status === true && response.data) {
          this.totalProducts = Array.isArray(response.data) ? response.data.length : 0;
          this.topProducts = Array.isArray(response.data)
            ? response.data.slice(0, 5).map((product: any) => ({
                product_name: product.product_name || 'Product',
                category_name: product.category_name || 'Category',
                sales_count: Math.floor(Math.random() * 100),
                total_revenue: (Math.random() * 5000).toFixed(2)
              }))
            : [];
          this.productGrowth = 12; // Mock growth
        }
      },
      error: (error) => {
        console.error('Error loading products:', error);
        this.totalProducts = 0;
      }
    });

    // Fetch all users data
    this.userService.getUsers().subscribe({
      next: (response: any) => {
        if (response.status === true && response.data) {
          this.totalUsers = Array.isArray(response.data) ? response.data.length : 0;
          this.activeUsers = Math.floor(this.totalUsers * 0.35);
          this.newUsersMonth = Math.floor(this.totalUsers * 0.08);
          this.userGrowth = 8; // Mock growth
        }
      },
      error: (error) => {
        console.error('Error loading users:', error);
        this.totalUsers = 0;
      }
    });

    // Fetch categories
    this.categoryService.getCategories().subscribe({
      next: (response: any) => {
        if (response.status === true && response.data) {
          this.totalCategories = Array.isArray(response.data) ? response.data.length : 0;
          this.topCategories = Array.isArray(response.data)
            ? response.data.slice(0, 5).map((category: any, index: number) => ({
                category_name: category.category_name || 'Category',
                product_count: Math.floor(Math.random() * 50),
                percentage: Math.floor(Math.random() * 100)
              }))
            : [];
        }
      },
      error: (error) => {
        console.error('Error loading categories:', error);
        this.totalCategories = 0;
      }
    });

    // Load mock orders data
    this.loadMockOrders();

    // Calculate revenues
    this.calculateRevenue();
  }

  private loadMockOrders(): void {
    // Mock recent orders data
    this.recentOrders = [
      {
        order_id: 1001,
        customer_name: 'John Doe',
        total_amount: 250.50,
        order_status: 'Completed',
        created_date: '2024-04-25'
      },
      {
        order_id: 1002,
        customer_name: 'Jane Smith',
        total_amount: 175.00,
        order_status: 'Processing',
        created_date: '2024-04-24'
      },
      {
        order_id: 1003,
        customer_name: 'Mike Johnson',
        total_amount: 425.75,
        order_status: 'Completed',
        created_date: '2024-04-23'
      },
      {
        order_id: 1004,
        customer_name: 'Sarah Williams',
        total_amount: 89.99,
        order_status: 'Pending',
        created_date: '2024-04-22'
      },
      {
        order_id: 1005,
        customer_name: 'Robert Brown',
        total_amount: 599.99,
        order_status: 'Completed',
        created_date: '2024-04-21'
      }
    ];
    this.orderGrowth = 15; // Mock growth
  }

  private calculateRevenue(): void {
    this.totalRevenue = this.recentOrders.reduce((sum, order) => {
      return sum + (order.total_amount || 0);
    }, 0);
    this.revenueGrowth = 22; // Mock growth
  }
}
