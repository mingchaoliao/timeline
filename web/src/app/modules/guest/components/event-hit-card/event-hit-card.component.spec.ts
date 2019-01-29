import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EventHitCardComponent } from './event-hit-card.component';

describe('EventHitCardComponent', () => {
  let component: EventHitCardComponent;
  let fixture: ComponentFixture<EventHitCardComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EventHitCardComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EventHitCardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
