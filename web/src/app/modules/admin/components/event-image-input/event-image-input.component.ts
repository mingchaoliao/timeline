import {Component, forwardRef, OnInit} from '@angular/core';
import {ControlValueAccessor, NG_VALUE_ACCESSOR} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {Url} from '../../../core/shared/classes/url';
import {ImageService} from '../../../core/shared/services/image.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {Image} from '../../../core/shared/models/image';

@Component({
  selector: 'app-event-image-input',
  templateUrl: './event-image-input.component.html',
  styleUrls: ['./event-image-input.component.css'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => EventImageInputComponent),
      multi: true
    }
  ]
})
export class EventImageInputComponent
  implements OnInit, ControlValueAccessor {

  public images: Array<Image> = [];
  public imageUploadStatus: any = {};

  private propagateChange = (_: any) => {
  };

  constructor(public common: CommonService, private imageService: ImageService) {

  }

  getImageUrl(image): string {
    if(image && image.eventId) {
      return Url.getImage(image.path);
    } else {
      return Url.getTempImage(image.path);
    }
  }

  ngOnInit() {

  }

  registerOnChange(fn: any): void {
    this.propagateChange = fn;
  }

  registerOnTouched(fn: any): void {
  }

  setDisabledState(isDisabled: boolean): void {
  }

  writeValue(obj: any): void {
    if (obj !== null) {
      this.images = obj;
    }
  }

  changeFile(event, index) {
    const newImageFile = event.srcElement.files[0];
    this.imageUploadStatus[index] = 'Uploading';
    this.imageService.upload(newImageFile).subscribe(
      image => {
        this.images[index] = image;
        this.imageUploadStatus[index] = 'Uploaded';
       this.propagateChange(this.images);
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to upload image'));
      }
    );
  }

  changeDescription(event, index) {
    const newDescription = event.srcElement.value;
    this.imageUploadStatus[index] = 'Updating';
    this.imageService.updateDescription(this.images[index].id, newDescription).subscribe(
      image => {
        this.images[index] = image;
        this.imageUploadStatus[index] = 'Updated';
        this.propagateChange(this.images);
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to update image'));
      }
    );
  }

  addImage() {
    this.images.push(null);
  }

  onDelete(index) {
    this.images.splice(index, 1);
    this.imageUploadStatus[index] = undefined;
    this.propagateChange(this.images);
  }
}
