import {async, ComponentFixture, TestBed} from '@angular/core/testing';

import {ImportEventComponent} from './import-event.component';

describe('ImportEventComponent', () => {
  let component: ImportEventComponent;
  let fixture: ComponentFixture<ImportEventComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ImportEventComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ImportEventComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
