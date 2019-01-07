import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ConfigurePeriodsComponent } from './configure-periods.component';

describe('ConfigurePeriodsComponent', () => {
  let component: ConfigurePeriodsComponent;
  let fixture: ComponentFixture<ConfigurePeriodsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ConfigurePeriodsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ConfigurePeriodsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
