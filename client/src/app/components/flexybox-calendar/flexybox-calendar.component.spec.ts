import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FlexyboxCalendarComponent } from './flexybox-calendar.component';

describe('FlexyboxCalendarComponent', () => {
  let component: FlexyboxCalendarComponent;
  let fixture: ComponentFixture<FlexyboxCalendarComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FlexyboxCalendarComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FlexyboxCalendarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
