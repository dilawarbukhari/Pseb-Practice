import { Routes } from '@angular/router';
import { LoginComponent } from './auth/login/login.component';
import { RegisterComponent } from './auth/register/register.component';
import { DashboardComponent } from './pages/dashboard/dashboard.component';
import { PageComponent } from './pages/page.component';
import { CategoryComponent } from './pages/category/category.component';
import { SidebarComponent } from './sidebar/sidebar.component';
import { ContentComponent } from './content/content.component';
import { ProductComponent } from './pages/product/product.component';
import { UsermanagementComponent } from './pages/usermanagement/usermanagement.component';
import { SettingComponent } from './pages/setting/setting.component';
import { RolepermissionComponent } from './pages/rolepermission/rolepermission.component';
import { PermissionComponent } from './pages/permission/permission.component';
import { ForgetpasswordComponent } from './auth/forgetpassword/forgetpassword.component';
import { UserproductComponent } from './pages/userproduct/userproduct.component';

import { OrderComponent } from './pages/orders/order.component';
import { OrdermanagementComponent } from './pages/ordermanagement/ordermanagement.component';
import { OrderdetailComponent } from './pages/orderdetail/orderdetail.component';
import { ProductdetailComponent } from './pages/productdetail/productdetail.component';
import { VerifyEmailComponent } from './auth/verify-email/verify-email.component';
import { TrackorderComponent } from './pages/trackorder/trackorder.component';
import { FeedbackComponent } from './feedback/feedback.component';
import { InvoicemanagementComponent } from './pages/invoicemanagement/invoicemanagement.component';



export const routes: Routes = [

  { path: 'login', component: LoginComponent },
  { path: 'forgotpassword', component: ForgetpasswordComponent },
  { path: 'register', component: RegisterComponent },
   {path:'verify-email',component:VerifyEmailComponent},
  { path: '', component: LoginComponent },
  {path : 'feedback/:orderId', component:FeedbackComponent},

  {
    path: 'pages',
    component: PageComponent,
    children: [

      { path: '', component: DashboardComponent },
      { path: 'user', component: UsermanagementComponent },
      { path: 'dashboard', component: DashboardComponent },
      { path: 'category', component: CategoryComponent },
      {path:'productadmin',component:ProductdetailComponent},
      { path: 'product', component: ProductComponent },
      { path: 'userproduct', component: UserproductComponent },
      { path: 'myorders', component: OrderComponent },
     { path:'trackorder', component:TrackorderComponent},
     { path:'invoice', component:InvoicemanagementComponent},
     { path: 'ordermanagementadmin', component: OrderdetailComponent },
       { path: 'ordermanagement', component: OrdermanagementComponent },
      { path: 'settings', component: SettingComponent },
      { path: 'rolepermission', component: RolepermissionComponent },
      { path: 'permission', component: PermissionComponent },

    ]
  }

];
