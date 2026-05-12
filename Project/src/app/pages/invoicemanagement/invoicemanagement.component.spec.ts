import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InvoicemanagementComponent } from './invoicemanagement.component';

describe('InvoicemanagementComponent', () => {
  let component: InvoicemanagementComponent;
  let fixture: ComponentFixture<InvoicemanagementComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InvoicemanagementComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(InvoicemanagementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
