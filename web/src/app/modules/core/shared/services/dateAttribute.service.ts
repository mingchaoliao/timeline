import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {DateAttribute} from '../models/dateAttribute';
import {Url} from '../classes/url';

@Injectable()
export class DateAttributeService {

  constructor(private httpService: HttpService) {
  }

  public get(): Observable<Array<DateAttribute>> {
    return new Observable<Array<DateAttribute>>(
      observer => {
        this.httpService.get(Url.getDateAttribute(), {}).subscribe(
          responseBody => observer.next(DateAttribute.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public create(dateAttribute: string): Observable<DateAttribute> {
    return new Observable<DateAttribute>(
      observer => {
        this.httpService.post(Url.createDateAttribute(), {}, {value: dateAttribute}).subscribe(
          responseBody => observer.next(DateAttribute.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public bulkCreate(dateAttributes: Array<string>): Observable<Array<DateAttribute>> {
    return new Observable<Array<DateAttribute>>(
      observer => {
        this.httpService.post(Url.bulkCreateDateAttribute(), {}, {values: dateAttributes}).subscribe(
          responseBody => observer.next(DateAttribute.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

}
