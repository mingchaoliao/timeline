import {Component, Input, OnInit} from '@angular/core';
import {AbstractControl, FormBuilder, FormGroup, ValidatorFn, Validators} from '@angular/forms';
import * as moment from 'moment';
import {NgbModal} from '@ng-bootstrap/ng-bootstrap';
import {CommonService} from '../../../core/shared/services/common.service';
import {Router} from '@angular/router';
import {PeriodService} from '../../../core/shared/services/period.service';
import {CatalogService} from '../../../core/shared/services/catalog.service';
import {DateAttributeService} from '../../../core/shared/services/dateAttribute.service';
import {DateFormatService} from '../../../core/shared/services/dateFormat.service';
import {EventService} from '../../../core/shared/services/event.service';

export function dateValidator(): ValidatorFn {
    return (control: AbstractControl): { [key: string]: any } | null => {
        return control.value == null || moment(control.value).isValid() ? null : {'invalidDate': {value: control.value}};
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

    public addPeriod = (name) => {
        return new Promise((resolve) => {
            this.addPeriodLoading = true;
            setTimeout(() => {
                this.periodService.create(name).subscribe(
                    period => {
                        resolve({id: period.id, value: period.value});
                        this.addPeriodLoading = false;
                    },
                    e => {
                        // TODO: handle exception
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
                    },
                    e => {
                        // TODO: handle exception
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
                    },
                    e => {
                        // TODO: handle exception
                    }
                );
            }, 100);
        });
    };
    private periodOptionsKvMap: any = {};
    private dateAttributeOptionsKvMap: any = {};
    private catalogOptionsKvMap: any = {};
    private dateFormats: any = [];
    private dateFormatKvMap: any = {};

    constructor(
        public formBuilder: FormBuilder,
        private modalService: NgbModal,
        private common: CommonService,
        private router: Router,
        private periodService: PeriodService,
        private catalogService: CatalogService,
        private dateAttributeService: DateAttributeService,
        private dateFormatService: DateFormatService,
        private eventService: EventService
    ) {
        this.dateAttributeService.get().subscribe(
            dateAttributes => {
                this.dateAttributeOptions = dateAttributes;
                this.dateAttributeOptionsKvMap = this.common.kvArrToMap(this.dateAttributeOptions);
            },
            e => {
                // TODO: handle exception
            }
        );

        this.periodService.getTypeahead().subscribe(
            periods => {
                this.periodOptions = periods;
                this.periodOptionsKvMap = this.common.kvArrToMap(this.periodOptions);
            },
            error => {
                // TODO: handle error
            }
        );

        this.catalogService.get().subscribe(
            catalogs => {
                this.catalogOptions = catalogs;
                this.catalogOptionsKvMap = this.common.kvArrToMap(this.catalogOptions);
            },
            e => {
                // TODO: handle exception
            }
        );

        this.dateFormatService.get().subscribe(
            dateFormats => {
                this.dateFormats = dateFormats;
                this.dateFormatKvMap = this.common.kvArrToMap(this.dateFormats, 'value', 'id');
            },
            e => {
                // TODO: handle exception
            }
        );
    }

    ngOnInit() {
        this.createEventForm = this.formBuilder.group({
            'startDate': [this.eventData ? this.eventData.startDate : null, [
                Validators.required,
                Validators.pattern('(^[0-9]{4}$)|(^[0-9]{4}-[0-9]{2}$)|(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)'),
                dateValidator()
            ]],
            'startDateAttributeId': [this.eventData ? (this.eventData.startDateAttribute ? this.eventData.startDateAttribute.id : null) : null, []],
            'endDate': [this.eventData ? this.eventData.endDate : null, [
                Validators.pattern('(^[0-9]{4}$)|(^[0-9]{4}-[0-9]{2}$)|(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)'),
                dateValidator()
            ]],
            'endDateAttributeId': [this.eventData ? (this.eventData.endDateAttribute ? this.eventData.endDateAttribute.id : null) : null, []],
            'periodId': [this.eventData ? (this.eventData.period ? this.eventData.period.id : null) : null, []],
            'catalogs': [this.eventData ? this.eventData.catalogs.map(function (catalog) {
                return catalog.id;
            }) : null, []],
            'content': [this.eventData ? this.eventData.content : null, [Validators.required]],
            'images': [this.eventData ? this.eventData.images.map(function (image) {
                return {path: image.path, description: image.description};
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

    onSubmit() {
        this.isSubmitted = true;
        if (!this.createEventForm.valid) {
            return;
        }
        const requestBody = this.createEventForm.value;
        if (requestBody.images === null) {
            requestBody.images = [];
        }
        for (let i = 0; i < requestBody.images; i++) {
            requestBody.images[i] = {
                path: requestBody.images[i].path,
                description: requestBody.images[i].description
            };
        }
        requestBody['startDateFormatId'] = this.dateFormatKvMap[this.common.getFormatByDateStr(requestBody.startDate)];
        if (requestBody['endDate'] !== null) {
            requestBody['endDateFormatId'] = this.dateFormatKvMap[this.common.getFormatByDateStr(requestBody.endDate)];
        }

        if (this.eventData !== null && this.eventData['id']) {
            this.eventService.update(Number(this.eventData['id']), requestBody).subscribe(
                event => {
                    this.router.navigate(['/']);
                },
                error => {
                    // TODO: handle exception
                }
            );
        } else {
            this.eventService.create(requestBody).subscribe(
                event => {
                    this.router.navigate(['/']);
                },
                error => {
                    // TODO: handle exception
                }
            );
        }
    }

    onPreview(modal) {
        this.isSubmitted = true;
        if (!this.createEventForm.valid) {
            return;
        }
        this.previewData = this.createEventForm.value;

        this.previewData.period = {
            value: this.periodOptionsKvMap[this.previewData.periodId]
        };

        if (this.previewData.catalogs) {
            const catalogs = [];
            for (const i of this.previewData.catalogs) {
                catalogs.push({value: this.catalogOptionsKvMap[i]});
            }
            this.previewData.catalogs = catalogs;
        }

        this.previewData['images'] = this.previewData.images;

        this.modalService.open(modal, {size: 'lg'});
    }
}
