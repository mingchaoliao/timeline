import {async, ComponentFixture, TestBed} from '@angular/core/testing';

import {SearchEventComponent} from './search.component';

describe('SearchComponent', () => {
  let component: SearchEventComponent;
  let fixture: ComponentFixture<SearchEventComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [SearchEventComponent]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SearchEventComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
