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


export const routes: Routes = [

{ path: 'login', component: LoginComponent },
  { path: 'forgotpassword', component: ForgetpasswordComponent},
{ path: 'register', component: RegisterComponent },
{ path: '', component: LoginComponent },

{
  path: 'pages',
  component: PageComponent,
  children: [

    { path: '', component: DashboardComponent },
     { path: 'user', component: UsermanagementComponent },
    { path: 'dashboard', component: DashboardComponent },
    { path: 'category', component: CategoryComponent },
    { path: 'product', component: ProductComponent },
    { path: 'settings', component: SettingComponent },
     { path: 'rolepermission', component: RolepermissionComponent },
     { path: 'permission', component: PermissionComponent },
  
  ]
}

];
