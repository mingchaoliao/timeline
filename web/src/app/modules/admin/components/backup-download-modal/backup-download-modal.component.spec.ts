import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BackupDownloadModalComponent } from './backup-download-modal.component';

describe('BackupDownloadModalComponent', () => {
  let component: BackupDownloadModalComponent;
  let fixture: ComponentFixture<BackupDownloadModalComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BackupDownloadModalComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BackupDownloadModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
