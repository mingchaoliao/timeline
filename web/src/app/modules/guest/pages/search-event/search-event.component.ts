import {AfterViewInit, Component, OnDestroy, OnInit} from '@angular/core';
import {ActivatedRoute, NavigationEnd, NavigationExtras, Router} from '@angular/router';
import {CommonService} from '../../../core/shared/services/common.service';
import * as moment from 'moment';
import {EventService} from '../../../core/shared/services/event.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-search-event',
  templateUrl: './search-event.component.html',
  styleUrls: ['./search-event.component.css']
})
export class SearchEventComponent implements OnInit, AfterViewInit, OnDestroy {
  public events: any = [];
  public pageSize = 10;
  public total = 10;
  public page = 1;
  public routerSubscriber = null;

  constructor(
    private common: CommonService,
    private route: ActivatedRoute,
    private router: Router,
    private eventService: EventService
  ) {
    this.routerSubscriber = this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd && event.url.startsWith('/app/event/search')) {
        const params = this.route.snapshot.queryParams;
        this.page = params['page'] ? parseInt(params['page'], 10) : 1;
        this.search(params);
      }
    });
  }

  ngOnDestroy() {
    if (this.routerSubscriber) {
      this.routerSubscriber.unsubscribe();
    }
  }

  public onPageChange(page) {
    if (parseInt(this.route.snapshot.queryParams['page'], 10) === this.page) {
      return;
    }

    this.events = [];
    const navigationExtras: NavigationExtras = {
      queryParams: {
        ...this.route.snapshot.queryParams,
        ...{page: page}
      }
    };
    this.router.navigate([], navigationExtras);
  }

  ngOnInit() {

  }

  ngAfterViewInit() {

  }

  private search(params: any) {
    const startDateFrom = params['startDateFrom'];
    const startDateTo = params['startDateTo'];
    const endDateFrom = params['endDateFrom'];
    const endDateTo = params['endDateTo'];
    const period = params['period'];
    const catalogs = params['catalogs'];
    const content = params['content'];
    const page = params['page'] ? params['page'] : 1;

    try {
      this.validateDate(startDateFrom);
      this.validateDate(startDateTo);
      this.validateDate(endDateFrom);
      this.validateDate(endDateTo);
      this.validatePeriod(period);
      this.validateCatalogs(catalogs);
      this.validatePage(page);

      this.eventService.search(
        startDateFrom,
        startDateTo,
        endDateFrom,
        endDateTo,
        period,
        catalogs,
        content,
        page
      ).subscribe(
        events => {
          this.total = events['total'];
          this.events = events;
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to search events'));
        }
      );
    } catch (error) {
      // TODO: handle error
    }
  }

  private validateDate(str: string) {
    if (str !== null && str !== undefined) {
      if (str.match('^[0-9]{4}-[0-9]{2}-[0-9]{2}$').length === 0 || !moment(str).isValid()) {
        throw new Error('Invalid date "' + str + '"');
      }
    }
  }

  private validatePeriod(str: string) {
    if (str !== null && str !== undefined) {
      if (isNaN(Number(str))) {
        throw new Error('Invalid period id "' + str + '"');
      }
    }
  }

  private validatePage(str: string) {
    if (str !== null && str !== undefined) {
      const num = Number(str);
      if (isNaN(num) || num < 1) {
        throw new Error('Invalid limit "' + str + '"');
      }
    }
  }

  private validateCatalogs(str: string) {
    if (str !== null && str !== undefined) {
      const arr = str.split(',');
      for (const i of arr) {
        if (isNaN(Number(i))) {
          throw new Error('Invalid catalog id "' + i + '"');
        }
      }
    }
  }

}
