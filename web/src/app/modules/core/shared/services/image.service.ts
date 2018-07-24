import {Injectable} from '@angular/core';
import {HttpService} from './http.service';
import {environment} from '../../../../../environments/environment';
import {Observable} from 'rxjs';
import {ImageUploadReceipt} from '../models/imageUploadReceipt';

@Injectable()
export class ImageService {

  constructor(private httpService: HttpService) {
  }

  public upload(imageFile: File): Observable<ImageUploadReceipt> {
    const imageForm = new FormData();
    imageForm.append('image', imageFile);
    return new Observable<ImageUploadReceipt>(
      observer => {
        this.httpService.post(
          environment.wsRoot + '/image',
          {},
          imageForm,
          {}
        ).subscribe(
          responseBody => observer.next(ImageUploadReceipt.fromJson(responseBody)),
          error => observer.error(error),
          () => observer.complete()
        );
      }
    );
  }
}
