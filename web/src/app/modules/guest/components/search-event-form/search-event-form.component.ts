import {Component, OnInit, ViewChild} from '@angular/core';
import * as moment from 'moment';
import {AbstractControl, FormBuilder, FormGroup, ValidatorFn} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {ActivatedRoute, NavigationEnd, NavigationExtras, Router} from '@angular/router';
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
        'startDate': [query['startDate'], []],
        'content': [query['content'], []],
        'period': [null, []],
        'catalogs': [null, []],
      });
    });

    this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd) {
        const query = this.route.snapshot.queryParams;

        if (this.periodOptions && this.periodOptions.length) {
          this.setPeriodByLabel(query['period']);
        }

        if (this.catalogOptions && this.catalogOptions.length) {
          this.setCatalogByLabels(query['catalogs']);
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
          this.setPeriodByLabel(this.route.snapshot.queryParams['period']);
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
          this.setCatalogByLabels(this.route.snapshot.queryParams['catalogs']);
        }
    );
  }

  setPeriodByLabel(label: string) {
    if (label) {
      setTimeout(() => {
        const option = this.periodNgSelect.itemsList.findByLabel(label);
        if (option) {
          this.periodNgSelect.select(option);
        }
      }, 10);
    }
  }

  setCatalogByLabels(labelStr: string) {
    setTimeout(() => {
      if (labelStr) {
        const labels = labelStr.split(',');
        for (const label of labels) {
          const option = this.catalogNgSelect.itemsList.findByLabel(label);
          if (option) {
            this.catalogNgSelect.select(option);
          }
        }
      }
    }, 10);
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
        queryParams['startDate'] = startDate;
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
