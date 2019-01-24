import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {Event} from '../models/event';
import {Url} from '../classes/url';
import {environment} from '../../../../../environments/environment';

@Injectable()
export class EventService {
  constructor(private httpService: HttpService) {
  }

  get(page: number = 1, pageSize: number = 10): Observable<Array<Event>> {
    const query = {
      page: page,
      pageSize: pageSize
    };

    return new Observable<Array<Event>>(
      observer => {
        this.httpService.get(Url.getEvents(), query, {}, true)
          .subscribe(
            response => {
              let arr: Array<Event> = Event.fromArray(response['body']);
              arr['total'] = response['headers'].get('X-Total-Count');
              observer.next(arr);
            },
            error => {
              observer.error(error);
            },
            () => {
              observer.complete();
            }
          );
      }
    );
  }

  getById(id: number): Observable<Event> {
    return new Observable<Event>(
      observer => {
        this.httpService.get(Url.getEventById(id)).subscribe(
          event => {
            observer.next(Event.fromJson(event));
          },
          error => {
            observer.error(error);
          },
          () => {
            observer.complete();
          }
        );
      }
    );
  }

  search(
    startDateFrom: string,
    startDateTo: string,
    endDateFrom: string,
    endDateTo: string,
    period: string,
    catalogs: string,
    content: string,
    page: number = 1,
    pageSize: number = 10
  ): Observable<Array<Event>> {
    const query = {
      page: page,
      pageSize: pageSize
    };
    if (startDateFrom) {
      query['startDateFrom'] = startDateFrom;
    }
    if (startDateTo) {
      query['startDateTo'] = startDateTo;
    }
    if (endDateFrom) {
      query['endDateFrom'] = endDateFrom;
    }
    if (endDateTo) {
      query['endDateTo'] = endDateTo;
    }
    if (period) {
      query['period'] = period;
    }
    if (catalogs) {
      query['catalogs'] = catalogs;
    }
    if (content) {
      query['content'] = content;
    }

    return new Observable<Array<Event>>(
      observer => {
        this.httpService.get(Url.searchEvent(), query, {}, true).subscribe(
          response => {
            let arr: Array<Event> = Event.fromArray(response['body']);
            arr['total'] = response['headers'].get('X-Total-Count');
            observer.next(arr);
          },
          error => {
            observer.error(error);
          },
          () => {
            observer.complete();
          }
        );
      }
    );
  }

  create(event: any): Observable<Event> {
    return new Observable<Event>(
      observer => {
        this.httpService.post(Url.createEvent(), {}, event).subscribe(
          responseBody => observer.next(Event.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  update(id: number, event: any): Observable<Event> {
    return new Observable<Event>(
      observer => {
        this.httpService.put(Url.updateEventById(id), {}, event).subscribe(
          responseBody => observer.next(Event.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  bulkCreate(events: Array<any>): Observable<boolean> {
    return new Observable<boolean>(
      observer => {
        this.httpService.post(Url.bulkCreateEvents(), {}, events).subscribe(
          responseBody => observer.next(true),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  deleteById(id: number): Observable<null> {
    return new Observable(
      observer => {
        this.httpService.delete(Url.deleteEvent(id)).subscribe(
          success => observer.next(),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }
}
