import {Component, OnInit, ViewChild} from '@angular/core';
import * as moment from 'moment';
import {AbstractControl, FormBuilder, FormGroup, ValidatorFn, Validators} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {ActivatedRoute, NavigationEnd, NavigationExtras, Router} from '@angular/router';
import {PeriodService} from '../../../core/shared/services/period.service';
import {CatalogService} from '../../../core/shared/services/catalog.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {NgbDateStruct} from '@ng-bootstrap/ng-bootstrap';
import {NgOption} from '@ng-select/ng-select';
import {Typeahead} from '../../../core/shared/models/typeahead';
import {EventDate} from '../../../core/shared/models/event';

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

  @ViewChild('periodNgSelect') periodNgSelect;
  @ViewChild('catalogNgSelect') catalogNgSelect;

  public searchEventForm: FormGroup;
  public periodOptions: any = [];
  public catalogOptions: any = [];
  public isAdvancedSearchCollapsed = true;

  constructor(
    private formBuilder: FormBuilder,
    private common: CommonService,
    private router: Router,
    private periodService: PeriodService,
    private catalogService: CatalogService,
    private route: ActivatedRoute
  ) {
    this.route.queryParams.subscribe(query => {
      if (query['startDate'] || query['endDate'] || query['period'] || query['catalogs']) {
        this.isAdvancedSearchCollapsed = false;
      }

      this.searchEventForm = this.formBuilder.group({
        'startDate': [null, [
          dateValidator()
        ]],
        'endDate': [null, [
          dateValidator()
        ]],
        'content': [query['content'], []],
        'period': [null, []],
        'catalogs': [null, []],
      });
    });

    this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd) {
        const query = this.route.snapshot.queryParams;

        if (this.periodOptions && this.periodOptions.length) {
          this.setPeriodById(query['period']);
        }

        if (this.catalogOptions && this.catalogOptions.length) {
          this.setCatalogByIds(query['catalogs']);
        }
      }
    });

    this.periodService.getTypeahead().subscribe(
      periods => {
        this.periodOptions = periods;
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve periods'));
      },
      () => {
        this.setPeriodById(this.route.snapshot.queryParams['period']);
      }
    );

    this.catalogService.getTypeahead().subscribe(
      catalogs => {
        this.catalogOptions = catalogs;
      },
      e => {
        NotificationEmitter.emit(Notification.error(e.error.message, 'Unable to retrieve catalogs'));
      },
      () => {
        this.setCatalogByIds(this.route.snapshot.queryParams['catalogs']);
      }
    );
  }

  setPeriodById(id: string) {
    if (id) {
      setTimeout(() => {
        const option = this.findNgOptionById(this.periodNgSelect.itemsList.items, parseInt(id));
        if (option) {
          this.periodNgSelect.select(option);
        }
      }, 10);
    }
  }

  setCatalogByIds(catalogStr: string) {
    setTimeout(() => {
      if (catalogStr) {
        const ids = catalogStr.split(',').map((catalog) => catalog.trim());
        for (const id of ids) {
          const option = this.findNgOptionById(this.catalogNgSelect.itemsList.items, parseInt(id));
          if (option) {
            this.catalogNgSelect.select(option);
          }
        }
      }
    }, 10);
  }

  findNgOptionById(options: Array<NgOption>, id: number): NgOption {
    for (const option of options) {
      if ((<Typeahead>option.value).id === id) {
        return option;
      }
    }
    return null;
  }

  ngOnInit() {
  }

  onSearch() {
    if (this.searchEventForm.valid) {
      const queryParams = {
        page: 1
      };
      const startDate = this.searchEventForm.value.startDate;
      const endDate = this.searchEventForm.value.endDate;
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

      if (endDate) {
        queryParams['endDateFrom'] = moment({
          year: endDate['year'],
          month: endDate['month'] - 1,
          day: endDate['day']
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
