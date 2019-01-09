import {Component, OnInit} from '@angular/core';
import * as moment from 'moment';
import {AbstractControl, FormBuilder, FormGroup, ValidatorFn, Validators} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {NavigationExtras, Router} from '@angular/router';
import {PeriodService} from '../../../core/shared/services/period.service';
import {CatalogService} from '../../../core/shared/services/catalog.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

export function dateValidator(): ValidatorFn {
  return (control: AbstractControl): { [key: string]: any } | null => {

    return control.value == null ||
    control.value['year']
    && control.value['month']
    && control.value['day']
    && moment({
      year: control.value['year'],
      month: control.value['month'] - 1,
      day: control.value['day']
    }).isValid() ? null : {'invalidDate': {value: control.value}};
  };
}

@Component({
  selector: 'app-search-event-form',
  templateUrl: './search-event-form.component.html',
  styleUrls: ['./search-event-form.component.css']
})
export class SearchEventFormComponent implements OnInit {

  public searchEventForm: FormGroup;
  public periodOptions: any = [];
  public catalogOptions: any = [];

  constructor(
    private formBuilder: FormBuilder,
    private common: CommonService,
    private router: Router,
    private periodService: PeriodService,
    private catalogService: CatalogService
  ) {
    this.searchEventForm = this.formBuilder.group({
      'startDateFrom': [null, [
        dateValidator()
      ]],
      'startDateTo': [null, [
        dateValidator()
      ]],
      'endDateFrom': [null, [
        dateValidator()
      ]],
      'endDateTo': [null, [
        dateValidator()
      ]],
      'content': [null, []],
      'period': [null, []],
      'catalogs': [null, []],
    });

    this.periodService.getTypeahead().subscribe(
      periods => this.periodOptions = periods,
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve periods'));
      }
    );

    this.catalogService.getTypeahead().subscribe(
      catalogs => {
        this.catalogOptions = catalogs;
      },
      e => {
        NotificationEmitter.emit(Notification.error(e.error.message, 'Unable to retrieve catalogs'));
      }
    );
  }

  ngOnInit() {
  }

  onSearch() {
    if (this.searchEventForm.valid) {
      const queryParams = {
        page: 1
      };
      const startDateFrom = this.searchEventForm.value.startDateFrom;
      const startDateTo = this.searchEventForm.value.startDateTo;
      const endDateFrom = this.searchEventForm.value.endDateFrom;
      const endDateTo = this.searchEventForm.value.endDateTo;
      const period = this.searchEventForm.value.period;
      const catalogs = this.searchEventForm.value.catalogs;
      const content = this.searchEventForm.value.content;

      if (startDateFrom) {
        queryParams['startDateFrom'] = moment({
          year: startDateFrom['year'],
          month: startDateFrom['month'] - 1,
          day: startDateFrom['day']
        }).format('YYYY-MM-DD');
      }

      if (startDateTo) {
        queryParams['startDateTo'] = moment({
          year: startDateTo['year'],
          month: startDateTo['month'] - 1,
          day: startDateTo['day']
        }).format('YYYY-MM-DD');
      }

      if (endDateFrom) {
        queryParams['endDateFrom'] = moment({
          year: endDateFrom['year'],
          month: endDateFrom['month'] - 1,
          day: endDateFrom['day']
        }).format('YYYY-MM-DD');
      }

      if (endDateTo) {
        queryParams['endDateTo'] = moment({
          year: endDateTo['year'],
          month: endDateTo['month'] - 1,
          day: endDateTo['day']
        }).format('YYYY-MM-DD');
      }

      if (period) {
        queryParams['period'] = period;
      }

      if (catalogs) {
        queryParams['catalogs'] = catalogs.join(',');
      }

      if (content) {
        queryParams['content'] = content;
      }

      queryParams['page'] = 1;

      const navigationExtras: NavigationExtras = {
        queryParams: queryParams
      };
      this.router.navigate(['/app/event/search'], navigationExtras);
    }
  }
}
