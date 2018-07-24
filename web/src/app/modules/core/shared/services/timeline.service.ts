import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {Url} from '../classes/url';

@Injectable()
export class TimelineService {

  constructor(private httpService: HttpService) {
  }

  get(): Observable<any> {
    return new Observable<any>(
      observer => {
        this.httpService.get(Url.getTimeline()).subscribe(
          timeline => observer.next(timeline),
          error => observer.next({}),
          () => observer.complete()
        );
      }
    );
  }
}
