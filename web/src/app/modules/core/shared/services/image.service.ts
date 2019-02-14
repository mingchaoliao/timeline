import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {Observable} from 'rxjs';
import {Url} from '../classes/url';
import {Image} from '../models/image';

@Injectable()
export class ImageService {

  constructor(private httpService: HttpService) {
  }

  public upload(imageFile: File, description: string = null): Observable<Image> {
    const imageForm = new FormData();
    imageForm.append('image', imageFile);
    if (description) {
      imageForm.append('description', description);
    }
    return new Observable<Image>(
      observer => {
        this.httpService.post(
          Url.uploadImage(),
          {},
          imageForm,
          {}
        ).subscribe(
          responseBody => observer.next(Image.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }

  public updateDescription(id: number, description: string): Observable<Image> {
    return new Observable<Image>(
      observer => {
        this.httpService.put(
          Url.updateImageDescription(id),
          {},
          {
            description: description
          },
          {}
        ).subscribe(
          responseBody => observer.next(Image.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }
}
