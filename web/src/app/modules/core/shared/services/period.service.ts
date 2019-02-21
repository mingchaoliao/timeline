import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {Period} from '../models/period';
import {Url} from '../classes/url';
import {Typeahead} from "../models/typeahead";

@Injectable()
export class PeriodService {

    constructor(private httpService: HttpService) {
    }

    public getTypeahead(): Observable<Array<Typeahead>> {
        return new Observable<Array<Typeahead>>(
            observer => {
                this.httpService.get(Url.getPeriodTypeahead(), {}).subscribe(
                    responseBody => observer.next(Typeahead.fromArray(<Array<any>>responseBody)),
                    error => observer.error(error),
                    () => observer.complete()
                );
            }
        );
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

    public update(id: number, value: string, startDate: string): Observable<Period> {
        if (!startDate) {
            startDate = null;
        }
        return new Observable<Period>(
            observer => {
                this.httpService.put(Url.updatePeriod(id), {}, {
                    value: value,
                    startDate: startDate
                }).subscribe(
                    responseBody => observer.next(Period.fromJson(responseBody)),
                    error => observer.error(error),
                    () => observer.complete()
                );
            }
        );
    }

    public delete(id: number): Observable<boolean> {
        return new Observable<boolean>(
            observer => {
                this.httpService.delete(Url.deletePeriod(id)).subscribe(
                    responseBody => observer.next(true),
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
