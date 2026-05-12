import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';

import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../Service/shared.service';

@Component({
  selector: 'app-trackorder',
  imports: [CommonModule,FormsModule],
  templateUrl: './trackorder.component.html',
  styleUrl: './trackorder.component.css'
})
export class TrackorderComponent {
  trackResponseList :any ;
  searchText= ''    ;
 constructor(private _trackService : SharedService,private _toastr:ToastrService){

 }



  getProgressPercent() {
    const statusId = Number(this.trackResponseList?.status_Id);

    if (statusId >= 4) {
      return 100;
    }

    if (statusId === 3) {
      return 66;
    }

    if (statusId === 2) {
      return 33;
    }

    if (statusId === 1) {
      return 0;
    }

    return 0;
  }

  trackOrder(){
  if(!this.searchText){
    return ;
  }
    this._trackService.trackOrder(this.searchText).subscribe({
      next:(response) =>{
            debugger
   if(response[1].status == "200"){
    this.trackResponseList= response[0].data ;
   }
  if(response[1].status == "400"){
   this._toastr.error(response[0].message, 'Warning');
 this.trackResponseList= '' ;
  }
      },
      error:(error)=>{
        this._toastr.error(error.error , 'error');
      }
    })
  }
}
