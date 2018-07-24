import {async, ComponentFixture, TestBed} from '@angular/core/testing';

import {SearchEventFormComponent} from './search-event-form.component';

describe('SearchEventFormComponent', () => {
  let component: SearchEventFormComponent;
  let fixture: ComponentFixture<SearchEventFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [SearchEventFormComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SearchEventFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
