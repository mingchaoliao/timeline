import {Component, EventEmitter, Input, OnInit} from '@angular/core';
import {AbstractControl, FormBuilder, FormGroup, ValidatorFn, Validators} from '@angular/forms';
import {NgbModal} from '@ng-bootstrap/ng-bootstrap';
import {CommonService} from '../../../core/shared/services/common.service';
import {Router} from '@angular/router';
import {PeriodService} from '../../../core/shared/services/period.service';
import {CatalogService} from '../../../core/shared/services/catalog.service';
import {DateAttributeService} from '../../../core/shared/services/dateAttribute.service';
import {EventService} from '../../../core/shared/services/event.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {EventDate} from '../../../core/shared/models/event';
import {Image} from '../../../core/shared/models/image';

export function dateValidator(): ValidatorFn {
  return (control: AbstractControl): { [key: string]: any } | null => {
    return control.value == null || EventDate.validate(control.value) ? null : {'invalidDate': {value: control.value}};
  };
}

export function imageValidator(): ValidatorFn {
  return (control: AbstractControl): { [key: string]: any } | null => {
    const images = control.value;
    if (images === null) {
      return null;
    }
    for (let i = 0; i < images.length; i++) {
      if (images[i].path) {
        continue;
      }
      if (!images[i].file) {
        return {missingImageFile: {index: i}};
      }
    }
    return null;
  };
}

@Component({
  selector: 'app-create-event',
  templateUrl: './create-event.component.html',
  styleUrls: ['./create-event.component.css']
})
export class CreateEventComponent
    implements OnInit {

  @Input() eventData = null;

  public createEventForm: FormGroup;

  public dateAttributeOptions: any = [];
  public periodOptions: any = [];
  public catalogOptions: any = [];
  public addDateAttributeLoading = false;
  public addPeriodLoading = false;
  public addCatalogLoading = false;
  public previewData: any = {};
  public isSubmitted = false;

  private periodOptionsKvMap: any = {};
  private dateAttributeOptionsKvMap: any = {};
  private catalogOptionsKvMap: any = {};

  public addPeriod = (name) => {
    return new Promise((resolve) => {
      this.addPeriodLoading = true;
      setTimeout(() => {
        this.periodService.create(name).subscribe(
            period => {
              resolve({id: period.id, value: period.value});
              this.addPeriodLoading = false;
              NotificationEmitter.emit(Notification.success('Create successfully'));
            },
            error => {
              NotificationEmitter.emit(Notification.error(error.error.message, `Unable to create period "${name}"`));
            }
        );
      }, 100);
    });
  };

  public addDateAttribute = (name) => {
    return new Promise((resolve) => {
      this.addDateAttributeLoading = true;
      setTimeout(() => {
        this.dateAttributeService.create(name).subscribe(
            dateAttribute => {
              resolve({id: dateAttribute.id, value: dateAttribute.value});
              this.addDateAttributeLoading = false;
              NotificationEmitter.emit(Notification.success('Create successfully'));
            },
            error => {
              NotificationEmitter.emit(Notification.error(error.error.message, `Unable to create date attribute "${name}"`));
            }
        );
      }, 100);
    });
  };

  public addCatalog = (name) => {
    return new Promise((resolve) => {
      this.addCatalogLoading = true;
      setTimeout(() => {
        this.catalogService.create(name).subscribe(
            catalog => {
              resolve({id: catalog.id, value: catalog.value});
              this.addCatalogLoading = false;
              NotificationEmitter.emit(Notification.success('Create successfully'));
            },
            error => {
              NotificationEmitter.emit(Notification.error(error.error.message, `Unable to create catalog "${name}"`));
            }
        );
      }, 100);
    });
  };

  constructor(
      public formBuilder: FormBuilder,
      private modalService: NgbModal,
      private common: CommonService,
      private router: Router,
      private periodService: PeriodService,
      private catalogService: CatalogService,
      private dateAttributeService: DateAttributeService,
      private eventService: EventService
  ) {
    this.dateAttributeService.get().subscribe(
        dateAttributes => {
          this.dateAttributeOptions = dateAttributes;
          this.dateAttributeOptionsKvMap = this.common.kvArrToMap(this.dateAttributeOptions);
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve date attributes'));
        }
    );

    this.periodService.getTypeahead().subscribe(
        periods => {
          this.periodOptions = periods;
          this.periodOptionsKvMap = this.common.kvArrToMap(this.periodOptions);
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve periods'));
        }
    );

    this.catalogService.get().subscribe(
        catalogs => {
          this.catalogOptions = catalogs;
          this.catalogOptionsKvMap = this.common.kvArrToMap(this.catalogOptions);
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve catalogs'));
        }
    );
  }

  ngOnInit() {
    this.createEventForm = this.formBuilder.group({
      'startDate': [this.eventData && this.eventData.startDate ? this.eventData.startDate.date : null, [
        Validators.required,
        Validators.pattern('(^[0-9]{4}$)|(^[0-9]{4}-[0-9]{2}$)|(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)'),
        dateValidator()
      ]],
      'startDateAttributeId': [
        this.eventData ? (this.eventData.startDateAttribute ? this.eventData.startDateAttribute.id : null) : null, []
      ],
      'endDate': [this.eventData && this.eventData.endDate ? this.eventData.endDate.date : null, [
        Validators.pattern('(^[0-9]{4}$)|(^[0-9]{4}-[0-9]{2}$)|(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)'),
        dateValidator()
      ]],
      'endDateAttributeId': [this.eventData ? (this.eventData.endDateAttribute ? this.eventData.endDateAttribute.id : null) : null, []],
      'periodId': [this.eventData ? (this.eventData.period ? this.eventData.period.id : null) : null, []],
      'catalogIds': [this.eventData ? this.eventData.catalogs.map(function (catalog) {
        return catalog.id;
      }) : null, []],
      'content': [this.eventData ? this.eventData.content : null, [Validators.required]],
      'images': [this.eventData ? this.eventData.images.map(function (image) {
        return Image.fromJson(image);
      }) : null, [imageValidator()]]
    });
  }

  onStartDateChange() {
    const startDate = this.createEventForm.value.startDate;
    if (startDate && startDate.match(/^[0-9]{4}$/)) {
      this.createEventForm.controls.startDateAttributeId.setValue(1);
    } else {
      this.createEventForm.controls.startDateAttributeId.setValue(null);
    }
  }

  onEndDateChange() {
    const endDate = this.createEventForm.value.endDate;
    if (endDate && endDate.match(/^[0-9]{4}$/)) {
      this.createEventForm.controls.endDateAttributeId.setValue(1);
    } else {
      this.createEventForm.controls.endDateAttributeId.setValue(null);
    }
  }

  onSubmit(loading: EventEmitter<boolean>) {
    this.isSubmitted = true;
    if (!this.createEventForm.valid) {
      return;
    }
    const formValues = this.createEventForm.value;
    const requestBody = {
      startDate: formValues['startDate'],
      startDateAttributeId: formValues['startDateAttributeId'],
      endDate: formValues.endDate,
      endDateAttributeId: formValues.endDateAttributeId,
      content: formValues.content,
      periodId: formValues.periodId,
      catalogIds: formValues.catalogIds,
      imageIds: formValues.images.map((image: Image) => {
        return image.id;
      })
    };

    if (this.eventData !== null && this.eventData['id']) {
      loading.emit(true);
      this.eventService.update(Number(this.eventData['id']), requestBody).subscribe(
          event => {
            this.router.navigate(['/']);
            loading.emit(false);
            NotificationEmitter.emit(Notification.success('Update successfully'));
          },
          error => {
            loading.emit(false);
            NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to update event'));
          }
      );
    } else {
      loading.emit(true);
      this.eventService.create(requestBody).subscribe(
          event => {
            this.router.navigate(['/']);
            loading.emit(false);
            NotificationEmitter.emit(Notification.success('Create successfully'));
          },
          error => {
            loading.emit(false);
            NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to create event'));
          }
      );
    }
  }

  onPreview(modal) {
    this.isSubmitted = true;
    if (!this.createEventForm.valid) {
      return;
    }
    this.previewData = JSON.parse(JSON.stringify(this.createEventForm.value));

    this.previewData.period = this.periodOptionsKvMap[this.previewData.periodId];
    this.previewData.startDate = EventDate.createFromString(this.previewData.startDate);
    if (this.previewData.endDate) {
      this.previewData.endDate = EventDate.createFromString(this.previewData.endDate);
    }

    if (this.previewData.catalogIds) {
      const catalogs = [];
      for (const i of this.previewData.catalogIds) {
        catalogs.push(this.catalogOptionsKvMap[i]);
      }
      this.previewData.catalogs = catalogs;
    }

    this.previewData['images'] = this.previewData.images;

    this.modalService.open(modal, {size: 'lg'});
  }
}
