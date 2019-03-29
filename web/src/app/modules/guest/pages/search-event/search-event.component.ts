import {AfterViewInit, Component, HostListener, OnDestroy, OnInit} from '@angular/core';
import {ActivatedRoute, NavigationExtras, Router} from '@angular/router';
import {CommonService} from '../../../core/shared/services/common.service';
import {EventService} from '../../../core/shared/services/event.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {Bucket, EventSearchResult} from '../../../core/shared/models/eventSearchResult';
import {Facet, FacetLink} from '../../components/faceted-search-bar/faceted-search-bar.component';
import {TranslateService} from "@ngx-translate/core";
import {Subscription} from "rxjs";
import {LanguageEmitter} from "../../../core/shared/events/languageEmitter";
import {PeriodService} from "../../../core/shared/services/period.service";

@Component({
  selector: 'app-search-event',
  templateUrl: './search-event.component.html',
  styleUrls: ['./search-event.component.css']
})
export class SearchEventComponent implements OnInit, AfterViewInit, OnDestroy {
  public result: EventSearchResult;
  public pageSize = 10;
  public total = 10;
  public page = 1;
  public maxSize = 3;
  private windowResizeTimeoutId: number;
  private _yearLabelText: string;
  private _periodLabelText: string;
  private _catalogLabelText: string;
  private _getYearLabelTranslationSubscription: Subscription;
  private _getPeriodLabelTranslationSubscription: Subscription;
  private _getCatalogLabelTranslationSubscription: Subscription;
  private _facets: Array<Facet>;

  constructor(
      private common: CommonService,
      public route: ActivatedRoute,
      private router: Router,
      private eventService: EventService,
      private _translate: TranslateService,
      private _periodService: PeriodService
  ) {
    this.getTranslation();

    LanguageEmitter.emitter.subscribe((language) => {
      this.getTranslation();
    });

    this.route.queryParams.subscribe(
        params => {
          this.search(params);
        }
    );
  }

  @HostListener('window:resize', ['$event'])
  onResize(event) {
    clearTimeout(this.windowResizeTimeoutId);
    this.windowResizeTimeoutId = setTimeout(() => this.maxSize = this.getMaxSize(event.target.innerWidth), 250);
  }

  getMaxSize(width: number): number {
    if (width > 640) {
      width = 640;
    } else if (width < 479) {
      width = 479;
    }

    return Math.floor((width * 7 - 2870) / 161);
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
    this.maxSize = this.getMaxSize(window.innerWidth);
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
            this._facets = <Array<Facet>>[
              {
                buckets: result.dates.reverse(),
                name: this._yearLabelText,
                numDisplayed: 5,
                increment: 5,
                disable: this.route.snapshot.queryParams['startDate'] ? [this.route.snapshot.queryParams['startDate']] : []
              },
              {
                buckets: result.periods,
                name: this._periodLabelText,
                numDisplayed: 15,
                increment: 10,
                disable: this.route.snapshot.queryParams['period'] ? [this.route.snapshot.queryParams['period']] : []
              },
              {
                buckets: result.catalogs,
                name: this._catalogLabelText,
                numDisplayed: 15,
                increment: 10,
                disable: this.route.snapshot.queryParams['catalogs'] ? this.route.snapshot.queryParams['catalogs'].split(',') : []
              }
            ];

            this._periodService.getTypeahead().subscribe(
                typeahead => {
                  const map = {};
                  let i;
                  for (i = 0; i < typeahead.length; i++) {
                    map[typeahead[i].value] = i;
                  }
                  this._facets[1].buckets = this._facets[1].buckets.sort((a: Bucket, b: Bucket) => {
                    const indexA = map[a.value] === undefined ? -1 : map[a.value];
                    const indexB = map[b.value] === undefined ? -1 : map[b.value];
                    if (indexA > indexB) {
                      return 1;
                    } else if (indexA < indexB) {
                      return -1;
                    }
                    return 0;
                  });
                }
            );
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

  get yearLabelText(): string {
    return this._yearLabelText;
  }

  get periodLabelText(): string {
    return this._periodLabelText;
  }

  get catalogLabelText(): string {
    return this._catalogLabelText;
  }

  private getTranslation() {
    this.unsubscribeTranslation();
    this._getYearLabelTranslationSubscription = this._translate.get('year').subscribe((res: string) => {
      this._yearLabelText = res;
    });

    this._getPeriodLabelTranslationSubscription = this._translate.get('period').subscribe((res: string) => {
      this._periodLabelText = res;
    });

    this._getCatalogLabelTranslationSubscription = this._translate.get('catalogs').subscribe((res: string) => {
      this._catalogLabelText = res;
    });
  }

  private unsubscribeTranslation() {
    if (this._getYearLabelTranslationSubscription) {
      this._getYearLabelTranslationSubscription.unsubscribe();
    }
    if (this._getPeriodLabelTranslationSubscription) {
      this._getPeriodLabelTranslationSubscription.unsubscribe();
    }
    if (this._getCatalogLabelTranslationSubscription) {
      this._getCatalogLabelTranslationSubscription.unsubscribe();
    }
  }

  ngOnDestroy(): void {
    this.unsubscribeTranslation();
  }

  get facets(): Array<Facet> {
    return this._facets;
  }
}
