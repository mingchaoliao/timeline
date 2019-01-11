import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ConfigureCatalogsComponent } from './configure-catalogs.component';

describe('ConfigureCatalogsComponent', () => {
  let component: ConfigureCatalogsComponent;
  let fixture: ComponentFixture<ConfigureCatalogsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ConfigureCatalogsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ConfigureCatalogsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
