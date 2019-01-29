import {Component, forwardRef, OnInit} from '@angular/core';
import {ControlValueAccessor, FormBuilder, FormGroup, NG_VALUE_ACCESSOR} from '@angular/forms';
import * as moment from 'moment';
import {EventDate} from '../../../core/shared/models/event';

interface DateOption {
  value: number;
  displayedValue: string;
}

@Component({
  selector: 'app-event-datepicker',
  templateUrl: './event-datepicker.component.html',
  styleUrls: ['./event-datepicker.component.css'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => EventDatepickerComponent),
      multi: true
    }
  ]
})
export class EventDatepickerComponent implements OnInit, ControlValueAccessor {
  public yearOptions: Array<DateOption> = this.generateYearOptions();
  public monthOptions: Array<DateOption> = this.generateMonthOptions();
  public dayOptions: Array<DateOption> = [];

  public form: FormGroup;

  private propagateChange = (_: any) => {
  };

  constructor(private formBuilder: FormBuilder) {
    this.form = this.formBuilder.group({
      year: [null, []],
      month: [{value: null, disabled: true}, []],
      day: [{value: null, disabled: true}, []],
    });
  }

  generateYearOptions(): Array<DateOption> {
    const currentYear = moment().year();
    const startYear = 1900;
    const options: Array<DateOption> = [];
    for (let i = currentYear; i >= startYear; i--) {
      options.push({
        value: i,
        displayedValue: String(i)
      });
    }
    return options;
  }

  generateMonthOptions(): Array<DateOption> {
    const options: Array<DateOption> = [];
    for (let i = 1; i < 13; i++) {
      options.push({
        value: i,
        displayedValue: String(i)
      });
    }
    return options;
  }

  generateDayOptions(year: number, month: number): Array<DateOption> {
    const daysInMonth = moment(`${year}-${month}`, 'YYYY-MM').daysInMonth();

    const options: Array<DateOption> = [];
    for (let i = 1; i <= daysInMonth; i++) {
      options.push({
        value: i,
        displayedValue: String(i)
      });
    }
    return options;
  }

  onYearChange(event) {
    this.reconcile();
    this.onValueChange();
  }

  onMonthChange(event) {
    this.reconcile();
    this.onValueChange();
  }

  reconcile() {
    if (this.form.value.year) {
      this.form.controls.month.enable();
    } else {
      this.form.controls.month.setValue(null);
      this.form.controls.month.disable();
      this.form.controls.day.setValue(null);
      this.form.controls.day.disable();
    }

    if (this.form.value.month) {
      this.form.controls.day.enable();
      this.dayOptions = this.generateDayOptions(
        this.form.value.year,
        this.form.value.month
      );
    } else {
      this.form.controls.day.setValue(null);
      this.form.controls.day.disable();
    }
  }

  onValueChange() {
    const year = this.form.value['year'];
    let month = this.form.value['month'];
    let day = this.form.value['day'];

    if (!year) {
      return null;
    }
    let rtn = String(year);

    if (month) {
      month = this.padZero(month);
      rtn = `${rtn}-${month}`;
    }

    if (day) {
      day = this.padZero(day);
      rtn = `${rtn}-${day}`;
    }

    this.propagateChange(rtn);
  }

  padZero(num: number): string {
    const str = String(num);
    if (str.length === 2) {
      return str;
    }
    return `0${str}`;
  }

  ngOnInit() {
  }

  registerOnChange(fn: any): void {
    this.propagateChange = fn;
  }

  registerOnTouched(fn: any): void {
  }

  setDisabledState(isDisabled: boolean): void {
  }

  writeValue(obj: any): void {
    if (obj) {
      try {
        const eventDate = EventDate.createFromString(obj);
        const year = eventDate.getYear();
        const month = eventDate.getMonth();
        const day = eventDate.getDay();

        this.form.controls.year.setValue(year);
        if (month) {
          this.form.controls.month.setValue(month);
        } else {
          this.form.controls.month.setValue(null);
        }
        if (day) {
          this.form.controls.day.setValue(day);
        } else {
          this.form.controls.day.setValue(null);
        }
      } catch (e) {
        this.form.controls.year.setValue(null);
        this.form.controls.month.setValue(null);
        this.form.controls.day.setValue(null);
      }
    } else {
      this.form.controls.year.setValue(null);
      this.form.controls.month.setValue(null);
      this.form.controls.day.setValue(null);
    }

    this.reconcile();
  }

}
