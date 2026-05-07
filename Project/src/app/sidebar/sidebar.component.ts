import { Component, OnInit, signal } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from "@angular/router";
import { ToastrService } from 'ngx-toastr';
import { RoleService } from '../Service/role.service';
import { CommonService } from '../Service/common.service';
import { UserService } from '../Service/user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-sidebar',
  imports: [RouterLink, RouterLinkActive,FormsModule,CommonModule],
  templateUrl: './sidebar.component.html',
  styleUrl: './sidebar.component.css'
})
export class SidebarComponent implements OnInit {
userResponseList:any=[];
role='' ;
  //  isExpanded= signal(false) ;
   constructor(private _router: Router,private _toasterService: ToastrService,private commonService: CommonService,private userService:UserService) {

   }
   ngOnInit(): void {
this.getUserDetails()
   }
   logout(){
    localStorage.removeItem('accessToken');
    this._router.navigateByUrl('/login');
   }
  navigate(){
this._router.navigateByUrl('/pages/category');
  }


getUserDetails(){
  const userId = this.commonService.getUserId();
 this.userService.getUser(userId!).subscribe({
  next:(response:any)=>{
   this.userResponseList=response[0];
   this.role=this.userResponseList[0].role_name.trim();
  },
  error:(error:any)=>{
    console.error('Error fetching user details:', error);
  }
 });
}
  
}
