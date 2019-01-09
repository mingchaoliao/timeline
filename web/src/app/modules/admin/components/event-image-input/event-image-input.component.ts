import {Component, forwardRef, OnInit} from '@angular/core';
import {ControlValueAccessor, NG_VALUE_ACCESSOR} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {Url} from '../../../core/shared/classes/url';
import {UserService} from '../../../core/shared/services/user.service';
import {ImageService} from '../../../core/shared/services/image.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

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

  public images = [];

  private propagateChange = (_: any) => {
  };

  constructor(public common: CommonService, private imageService: ImageService) {

  }

  getImageUrl(path: string): string {
    return Url.getImageByPath(
      path,
      UserService.getCurrentUser() === null ? false : UserService.getCurrentUser().isAdmin
    );
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
    this.images[index].file = event.srcElement.files[0];
    this.propagateChange(this.images);
    this.images[index].uploadStatus = 'Uploading';
    this.imageService.upload(this.images[index].file).subscribe(
      imageUploadReceipt => {
        this.images[index].path = imageUploadReceipt.path;
        this.images[index].uploadStatus = 'Uploaded';
        this.propagateChange(this.images);
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to update image'));
      }
    );
  }

  changeDescription(event, index) {
    this.images[index].description = event.srcElement.value;
    this.propagateChange(this.images);
  }

  addImage() {
    this.images.push({
      file: null,
      description: null,
      uploadStatus: '',
      path: ''
    });
    this.propagateChange(this.images);
  }

  onDelete(index) {
    this.images.splice(index, 1);
    this.propagateChange(this.images);
  }
}
