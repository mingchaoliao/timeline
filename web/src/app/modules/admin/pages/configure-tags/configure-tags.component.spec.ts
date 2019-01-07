import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ConfigureTagsComponent } from './configure-tags.component';

describe('ConfigureTagsComponent', () => {
  let component: ConfigureTagsComponent;
  let fixture: ComponentFixture<ConfigureTagsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ConfigureTagsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ConfigureTagsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
