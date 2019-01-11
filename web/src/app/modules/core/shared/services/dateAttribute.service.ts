import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {DateAttribute} from '../models/dateAttribute';
import {Url} from '../classes/url';
import {Typeahead} from "../models/typeahead";

@Injectable()
export class DateAttributeService {

    constructor(private httpService: HttpService) {
    }

    public getTypeahead(): Observable<Array<Typeahead>> {
        return new Observable<Array<Typeahead>>(
            observer => {
                this.httpService.get(Url.getDateAttributeTypeahead(), {}).subscribe(
                    responseBody => observer.next(Typeahead.fromArray(<Array<any>>responseBody)),
                    error => observer.error(error),
                    () => observer.complete()
                );
            }
        );
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

    public update(id: number, value: string): Observable<DateAttribute> {
        return new Observable<DateAttribute>(
            observer => {
                this.httpService.put(Url.updateDateAttribute(), {}, {
                    id: id,
                    value: value
                }).subscribe(
                    responseBody => observer.next(DateAttribute.fromJson(responseBody)),
                    error => observer.error(error),
                    () => observer.complete()
                );
            }
        );
    }

    public delete(id: number): Observable<boolean> {
        return new Observable<boolean>(
            observer => {
                this.httpService.delete(Url.deleteDateAttribute(), {id: id}).subscribe(
                    responseBody => observer.next(true),
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
