import { CommonModule } from '@angular/common';
import { Component,OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { SharedService } from '../Service/shared.service';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-feedback',
  imports: [CommonModule,FormsModule],
  templateUrl: './feedback.component.html',
  styleUrl: './feedback.component.css'
})
export class FeedbackComponent implements OnInit {
  rating = 0;
  isSubmitted=0;
  feedbackdetail = "";
  showFeedbackModal = true;
  showThankYouModal = false;

  constructor(private _route: Router,private _sharedService: SharedService,private _toaster:ToastrService,private route: ActivatedRoute){

  }
ngOnInit(): void {
  this.checkReviewStatus();
}
  setRating(value: number) {
    this.rating = value;
  }

  closeFeedbackModal() {
    this.showFeedbackModal = false;
  }

  closeThankYouModal() {
    this.showThankYouModal = false;
    this._route.navigateByUrl('/login');
  }

  checkReviewStatus(){
    debugger
 const payload ={
      order_Id:this.route.snapshot.paramMap.get('orderId'),
      process : 'getReviewStatus'
    }
this._sharedService.getReviewStatus(payload).subscribe({
  next:(response)=>{
    debugger
   if(response[1].status == "200"){
    this.isSubmitted= response[0].data;
    if(this.isSubmitted == 0){
        this.showThankYouModal = false;
      this.showFeedbackModal = true;
    
    }
 this.showFeedbackModal = false;
     this.showThankYouModal = true;
   }
  },
    error: (error:any)=>{
        this._toaster.error(error.error , 'error');
      }
})
  }
  

  Feedback(){
    const payload ={
      order_Id:this.route.snapshot.paramMap.get('orderId'),
      rating: this.rating,
      feedbackdetail: this.feedbackdetail,
      process : 'Feedback'
    }
this._sharedService.Feedback(payload).subscribe({
  next:(response)=>{
    debugger
   if(response[1].status == "200"){
     this._toaster.success(response[0].message, 'success');
     this.rating=0;
     this.feedbackdetail=''; 
     this.showFeedbackModal = false;
     this.showThankYouModal = true;
     return;
   }
   this._toaster.error(response[0].message, 'Warning')
  },
    error: (error:any)=>{
        this._toaster.error(error.error , 'error');
      }
})
  }
}
