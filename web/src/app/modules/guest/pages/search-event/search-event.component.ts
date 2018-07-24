import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, NavigationExtras, Router} from '@angular/router';
import {CommonService} from '../../../core/shared/services/common.service';
import * as moment from 'moment';
import {EventService} from '../../../core/shared/services/event.service';

@Component({
  selector: 'app-search-event',
  templateUrl: './search-event.component.html',
  styleUrls: ['./search-event.component.css']
})
export class SearchEventComponent implements OnInit {
  public events: any = [];
  public total = 20;
  public page = 1;
  public pageSize = 10;
  public isLocked = false;

  constructor(
    private common: CommonService,
    private route: ActivatedRoute,
    private router: Router,
    private eventService: EventService
  ) {
    let page = this.route.snapshot.queryParams['page'];
    if (!page) {
      page = 1;
    }
    this.page = page;
    this.total = page * this.pageSize;
    this.route.queryParams.subscribe(
      params => {
        this.search(params);
      }
    );
  }

  public onPageChange(page) {
    if(!this.isLocked) {
      this.page = page;
      this.events = [];
      const param = this.route.snapshot.queryParams;

      const navigationExtras: NavigationExtras = {
        queryParams: {
          ...param,
          ...{page: this.page}
        }
      };
      this.router.navigate([], navigationExtras);
    }
  }

  ngOnInit() {
  }

  private search(params: any) {
    this.isLocked = true;
    const startDate = params['startDate'];
    const endDate = params['endDate'];
    const period = params['period'];
    const catalogs = params['catalogs'];
    const content = params['content'];
    const page = params['page'] ? params['page'] : 1;
    this.page = page;

    try {
      this.validateDate(startDate);
      this.validateDate(endDate);
      this.validatePeriod(period);
      this.validateCatalogs(catalogs);
      this.validatePage(page);

      this.eventService.search(
        startDate,
        endDate,
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
          // TODO: handle error
        },
        () => {
          this.isLocked = false;
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
