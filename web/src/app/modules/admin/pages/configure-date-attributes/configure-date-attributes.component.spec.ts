import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ConfigureDateAttributesComponent } from './configure-date-attributes.component';

describe('ConfigureDateAttributesComponent', () => {
  let component: ConfigureDateAttributesComponent;
  let fixture: ComponentFixture<ConfigureDateAttributesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ConfigureDateAttributesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ConfigureDateAttributesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
