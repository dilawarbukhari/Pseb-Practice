import { CommonModule } from '@angular/common';
import { Component,OnInit, ViewChild, viewChild } from '@angular/core';
import { RoleService } from '../../Service/role.service';
import { ToastrService } from 'ngx-toastr';
import { FormsModule } from '@angular/forms';
import { PermissionService } from '../../Service/permission.service';
import { CommonService } from '../../Service/common.service';

@Component({
  selector: 'app-rolepermission',
  imports: [CommonModule,FormsModule  ],
  templateUrl: './rolepermission.component.html',
  styleUrl: './rolepermission.component.css'
})
export class RolepermissionComponent implements OnInit {
@ViewChild ('roleModal') roleModal: any;
roleResponseList: any[] = [];
permissionsResponseList: any[] = [];
rolePermissionResponseList: any[] = [];
selectedPermissions: any []=[];
selectedRoleId: number | null = null;
permissions: any[] = [];
roleName='';
roleId=0;
 modal=document.getElementById('roleModal');
selectedRole: any;

IsEdit = false;
 constructor(private _roleService: RoleService,private _commonService: CommonService,private _toasterService : ToastrService,private _permissionService: PermissionService) { }

 ngOnInit(): void {
    this.getRoles();
    this.getPermission();
 }
 roles: any[] = [];
openModel(){
  this.IsEdit = false;
  this.roleName = '';
}
selectRole(role: any) {
  this.selectedRoleId = role.role_Id;
  this.getRolePermissions(role.role_Id);
}
isPermissionAssigned(permissionId: number): boolean {
  return this.rolePermissionResponseList?.some(
    (rp: any) => rp.Permission_Id === permissionId
  );
}
onPermissionChange(permissionId: number, event: any) {
  debugger
  if (event.target.checked) {
    this.selectedPermissions.push(permissionId);
  } else {
    this.selectedPermissions = this.selectedPermissions.filter(
      id => id !== permissionId
    );
  }
}
getRolePermissions(roleId: number) {
   this._roleService.getRolePermission(roleId).subscribe({
    next: (response) => {
    this.rolePermissionResponseList = response[0];
     this.selectedPermissions = this.rolePermissionResponseList.map(
        (rp: any) => rp.Permission_Id
          );
    },
    error: (error) => {
      if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  }); 
  }
savePermissions() {
    const payload = {
    role_Id: this.selectedRoleId,
    permission_Id: this.selectedPermissions
  };
this._roleService.updateRolePermission(payload).subscribe({
  next: (response) => {
    debugger
    if (response[1].status !== "200") {
      this._toasterService.error(response[0].message, 'Error');
    } 
      this._toasterService.success(response[0].message, 'Success');
  },
  error: (error) => {
    if (error.error?.response) {
      this._toasterService.error(error.error.response, 'Error');
    } 
}
});
}
openDeleteModal(role: any) {
  this.roleId = role.role_Id;
  this.roleName = role.role_name;

  let modal = new (window as any).bootstrap.Modal(
    document.getElementById('deleteModal')
  );
  modal.show();
}
deleteRole() {
 this._roleService.deleteRole(this.roleId).subscribe({
  next: (response) => {
    if (response[1].status !== "200") {
      this._toasterService.error(response[0].message, 'Error');
    }
      this._toasterService.success(response[0].message, 'Success');
      this.getRoles();
          this._commonService.closeModal('deleteModal');
  },
  error: (error) => {
    if (error.error?.response) {
      this._toasterService.error(error.error.response, 'Error');
    } 
  }
  })
}
updateRole() {
  if (!this.roleName.trim()) {
 this._toasterService.warning('Role name cannot be empty', 'Warning');
  }
  const updatedRole = {
    role_Id: this.selectedRoleId,
    role_name: this.roleName
  };
  this._roleService.updateRole(updatedRole).subscribe({
    next: (response) => {
      if (response[1].status !== "200") {
        this._toasterService.error(response[0].message, 'Error');
      } 
        this._toasterService.success(response[0].message, 'Success');
        this.getRoles();
        this.roleName = '';
         this._commonService.closeModal('roleModal');
      },
    error: (error) => {
      if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  })
}
editRole(role: any) {
 this.IsEdit = true;
  this.roleName = role.role_name;
  this.selectedRoleId = role.role_Id;
}
getRoles(){
this._roleService.getRoles().subscribe({
  next:(response:any)=>{
      this.roleResponseList =response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
getPermission(){
this._permissionService.getPermission().subscribe({
  next:(response:any)=>{
      this.permissionsResponseList =response[0];
  },
  error:(error)=>{
if(error.error?.response){
        this._toasterService.error(error.error.response, 'Error');
  } } })
}
saveRole() {
  if (!this.roleName.trim()) {
 this._toasterService.warning('Role name cannot be empty', 'Warning');
  }
  this._roleService.addRole(this.roleName).subscribe({
    next: (response) => {
      debugger
      if (response[1].status !== "200") {
        this._toasterService.error(response[0].message, 'Error');
      } 
        this._toasterService.success(response[0].message, 'Success');
        this.getRoles();
        this.roleName = '';
        this._commonService.closeModal('roleModal');
      },
    error: (error) => {
      if (error.error?.response) {
        this._toasterService.error(error.error.response, 'Error');
      }
    }
  })
}
}