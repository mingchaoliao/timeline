import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BackupDeleteConfirmationModalComponent } from './backup-delete-confirmation-modal.component';

describe('BackupDeleteConfirmationModalComponent', () => {
  let component: BackupDeleteConfirmationModalComponent;
  let fixture: ComponentFixture<BackupDeleteConfirmationModalComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BackupDeleteConfirmationModalComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BackupDeleteConfirmationModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
