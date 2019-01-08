import {Component, OnInit} from '@angular/core';
import * as moment from 'moment';
import {AbstractControl, FormBuilder, FormGroup, ValidatorFn, Validators} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {NavigationExtras, Router} from '@angular/router';
import {PeriodService} from '../../../core/shared/services/period.service';
import {CatalogService} from '../../../core/shared/services/catalog.service';

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
      'startDate': [null, [
        dateValidator()
      ]],
      'content': [null, []],
      'period': [null, []],
      'catalogs': [null, []],
    });

    this.periodService.getTypeahead().subscribe(
      periods => this.periodOptions = periods,
      error => {
        // TODO: handle error
      }
    );

    this.catalogService.get().subscribe(
      catalogs => {
        this.catalogOptions = catalogs;
      },
      e => {
        // TODO: handle exception
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
      const startDate = this.searchEventForm.value.startDate;
      const period = this.searchEventForm.value.period;
      const catalogs = this.searchEventForm.value.catalogs;
      const content = this.searchEventForm.value.content;

      if (startDate) {
        queryParams['startDate'] = moment({
          year: startDate['year'],
          month: startDate['month'] - 1,
          day: startDate['day']
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
