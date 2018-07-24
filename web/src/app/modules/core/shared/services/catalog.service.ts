import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {Catalog} from '../models/catalog';
import {Url} from '../classes/url';

@Injectable()
export class CatalogService {

  constructor(private httpService: HttpService) {
  }

  public get(): Observable<Array<Catalog>> {
    return new Observable<Array<Catalog>>(
      observer => {
        this.httpService.get(Url.getCatalog(), {}).subscribe(
          responseBody => observer.next(Catalog.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public create(catalog: string): Observable<Catalog> {
    return new Observable<Catalog>(
      observer => {
        this.httpService.post(Url.createCatalog(), {}, {value: catalog}).subscribe(
          responseBody => observer.next(Catalog.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public bulkCreate(catalogs: Array<string>): Observable<Array<Catalog>> {
    return new Observable<Array<Catalog>>(
      observer => {
        this.httpService.post(Url.bulkCreateCatalog(), {}, {values: catalogs}).subscribe(
          responseBody => observer.next(Catalog.fromArray(<Array<any>>responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

}
