import {AfterViewInit, Component, OnDestroy, OnInit} from '@angular/core';
import {ActivatedRoute, NavigationEnd, NavigationExtras, Router} from '@angular/router';
import {CommonService} from '../../../core/shared/services/common.service';
import * as moment from 'moment';
import {EventService} from '../../../core/shared/services/event.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {EventSearchResult} from '../../../core/shared/models/eventSearchResult';
import {FacetLink} from '../../components/faceted-search-bar/faceted-search-bar.component';

@Component({
  selector: 'app-search-event',
  templateUrl: './search-event.component.html',
  styleUrls: ['./search-event.component.css']
})
export class SearchEventComponent implements OnInit, AfterViewInit {
  public result: EventSearchResult;
  public pageSize = 10;
  public total = 10;
  public page = 1;

  constructor(
    private common: CommonService,
    public route: ActivatedRoute,
    private router: Router,
    private eventService: EventService
  ) {
    this.route.queryParams.subscribe(
      params => {
        this.search(params);
      }
    );
  }

  public onPageChange(page) {
    if (parseInt(this.route.snapshot.queryParams['page'], 10) === this.page) {
      return;
    }

    this.result = null;
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

  onFacetChange(facetLink: FacetLink) {
    const value = facetLink.value;
    if (facetLink.facetIndex === 0) { // Year
      this.router.navigate([], {
        queryParams: {startDate: value, page: 1},
        queryParamsHandling: 'merge'
      });
    } else if (facetLink.facetIndex === 1) { // Period
      this.router.navigate([], {
        queryParams: {period: value, page: 1},
        queryParamsHandling: 'merge'
      });
    } else if (facetLink.facetIndex === 2) { // Catalog
      this.router.navigate([], {
        queryParams: {catalogs: this.mergeCatalogs(this.route.snapshot.queryParams['catalogs'], value), page: 1},
        queryParamsHandling: 'merge'
      });
    }
  }

  mergeCatalogs(c1: string, c2: string): string {
    if (!c1) {
      return c2;
    }
    if (!c2) {
      return c1;
    }
    return c1 + ',' + c2;
  }

  private search(params: any) {
    const startDate = params['startDate'];
    const period = params['period'];
    const catalogs = params['catalogs'];
    const content = params['content'];
    const page = params['page'] ? params['page'] : 1;

    try {
      this.validatePage(page);

      this.eventService.search(
        startDate,
        period,
        catalogs,
        content,
        page
      ).subscribe(
        result => {
          this.pageSize = 10;
          this.page = this.route.snapshot.queryParams['page'];
          if (!this.page) {
            this.page = 1;
          }
          this.result = result;
          this.total = result.total;
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to search events'));
        }
      );
    } catch (error) {
      console.log(error);
      // TODO: handle error
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
}
