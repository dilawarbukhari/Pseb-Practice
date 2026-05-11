import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { TrackorderService } from '../../Service/trackorder.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-trackorder',
  imports: [CommonModule,FormsModule],
  templateUrl: './trackorder.component.html',
  styleUrl: './trackorder.component.css'
})
export class TrackorderComponent {
  trackResponseList :any ;
  searchText= ''    ;
 constructor(private _trackService : TrackorderService,private _toastr:ToastrService){

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
