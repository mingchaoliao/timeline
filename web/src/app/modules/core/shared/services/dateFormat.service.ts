import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {DateFormat} from '../models/dateFormat';
import {Url} from '../classes/url';

@Injectable()
export class DateFormatService {

  constructor(private httpService: HttpService) {
  }

  public get(): Observable<Array<DateFormat>> {
    return new Observable<Array<DateFormat>>(
      observer => {
        this.httpService.get(Url.getDateFormat(), {}).subscribe(
          responseBody => observer.next(DateFormat.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

}
