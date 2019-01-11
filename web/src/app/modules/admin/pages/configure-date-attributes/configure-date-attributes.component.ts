import {Component, EventEmitter, OnInit} from '@angular/core';
import {NgbModal, NgbModalRef} from '@ng-bootstrap/ng-bootstrap';
import {DateAttributeService} from '../../../core/shared/services/dateAttribute.service';
import {DateAttribute} from '../../../core/shared/models/dateAttribute';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-configure-date-attributes',
  templateUrl: './configure-date-attributes.component.html',
  styleUrls: ['./configure-date-attributes.component.css']
})
export class ConfigureDateAttributesComponent implements OnInit {
  public dateAttributes: Array<DateAttribute> = null;
  public createModalRef: NgbModalRef = null;

  constructor(
    public dateAttributeService: DateAttributeService,
    public modalService: NgbModal
  ) {
    dateAttributeService.get().subscribe(
      s => {
        this.dateAttributes = s;
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve date attributes'));
      }
    );
  }

  public delete(loading: EventEmitter<boolean>, index: number) {
    loading.emit(true);
    const dateAttribute = this.dateAttributes[index];
    this.dateAttributeService.delete(dateAttribute.id).subscribe(
      s => {
        this.dateAttributes.splice(index, 1);
        loading.emit(false);
        NotificationEmitter.emit(Notification.success('Delete successfully'));
      },
      error => {
        loading.emit(false);
        NotificationEmitter.emit(Notification.error(error.error.message, `Unable to delete date attribute with ID "${dateAttribute.id}"`));
      }
    );
  }

  public update(loading: EventEmitter<boolean>, index: number, value: string) {
    loading.emit(true);
    const dateAttribute = this.dateAttributes[index];
    this.dateAttributeService.update(dateAttribute.id, value).subscribe(
      s => {
        this.dateAttributes[index] = s;
        loading.emit(false);
        NotificationEmitter.emit(Notification.success('Update successfully'));
      },
      error => {
        loading.emit(false);
        NotificationEmitter.emit(Notification.error(error.error.message, `Unable to update date attribute with ID "${dateAttribute.id}"`));
      }
    );
  }

  public createNew(loading: EventEmitter<boolean>, value: string) {
    loading.emit(true);
    this.dateAttributeService.create(value).subscribe(
      s => {
        this.dateAttributes.push(s);
        this.createModalRef.close();
        loading.emit(false);
        NotificationEmitter.emit(Notification.success('Create successfully'));
      },
      error => {
        loading.emit(false);
        NotificationEmitter.emit(Notification.error(error.error.message, `Unable to create data attribute "${value}"`));
      }
    );
  }

  ngOnInit() {
  }
}
