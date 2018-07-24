import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {Period} from '../models/period';
import {Url} from '../classes/url';

@Injectable()
export class PeriodService {

  constructor(private httpService: HttpService) {
  }

  public get(): Observable<Array<Period>> {
    return new Observable<Array<Period>>(
      observer => {
        this.httpService.get(Url.getPeriod(), {}).subscribe(
          responseBody => observer.next(Period.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public create(period: string): Observable<Period> {
    return new Observable<Period>(
      observer => {
        this.httpService.post(Url.createPeriod(), {}, {value: period}).subscribe(
          responseBody => observer.next(Period.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public bulkCreate(periods: Array<string>): Observable<Array<Period>> {
    return new Observable<Array<Period>>(
      observer => {
        this.httpService.post(Url.bulkCreatePeriod(), {}, {values: periods}).subscribe(
          responseBody => observer.next(Period.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

}
