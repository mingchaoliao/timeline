import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FacetedSearchBarComponent } from './faceted-search-bar.component';

describe('FacetedSearchBarComponent', () => {
  let component: FacetedSearchBarComponent;
  let fixture: ComponentFixture<FacetedSearchBarComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FacetedSearchBarComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FacetedSearchBarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
