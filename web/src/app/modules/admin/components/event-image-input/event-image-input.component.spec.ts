import {async, ComponentFixture, TestBed} from '@angular/core/testing';

import {EventImageInputComponent} from './event-image-input.component';

describe('EventImageInputComponent', () => {
  let component: EventImageInputComponent;
  let fixture: ComponentFixture<EventImageInputComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [EventImageInputComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EventImageInputComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
