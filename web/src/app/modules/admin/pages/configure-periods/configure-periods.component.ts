import {Component, EventEmitter, OnInit} from '@angular/core';
import {PeriodService} from '../../../core/shared/services/period.service';
import {Period} from '../../../core/shared/models/period';
import {NgbModal, NgbModalRef} from '@ng-bootstrap/ng-bootstrap';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import * as moment from 'moment';

@Component({
    selector: 'app-configure-periods',
    templateUrl: './configure-periods.component.html',
    styleUrls: ['./configure-periods.component.css']
})
export class ConfigurePeriodsComponent implements OnInit {

    public periods: Array<Period> = null;
    public createModalRef: NgbModalRef = null;
    private readonly _createPeriodForm: FormGroup;
    private _updatePeriodFormMap: Map<number, FormGroup> = new Map<number, FormGroup>();

    constructor(
        public periodService: PeriodService,
        public modalService: NgbModal,
        private fb: FormBuilder
    ) {
        this._createPeriodForm = fb.group({
            value: [null, [Validators.required]]
        });
    }

    get createPeriodForm(): FormGroup {
        return this._createPeriodForm;
    }

    public delete(loading: EventEmitter<boolean>, index: number) {
        loading.emit(true);
        const period = this.periods[index];
        this.periodService.delete(period.id).subscribe(
            s => {
                this._updatePeriodFormMap.delete(period.id);
                this.periods.splice(index, 1);
                loading.emit(false);
                NotificationEmitter.emit(Notification.success('Delete successfully'));
            },
            error => {
                loading.emit(false);
                NotificationEmitter.emit(Notification.error(error.error.message, `Unable to delete period with ID "${period.id}"`));
            }
        );
    }

    public update(loading: EventEmitter<boolean>, index: number) {
        const period = this.periods[index];
        const form = this._updatePeriodFormMap.get(period.id);
        if (form.valid) {
            loading.emit(true);
            this.periodService.update(period.id, form.value['value'], form.value['startDate']).subscribe(
                s => {
                    this.periods[index] = s;
                    loading.emit(false);
                    NotificationEmitter.emit(Notification.success('Update successfully'));
                },
                error => {
                    loading.emit(false);
                    NotificationEmitter.emit(Notification.error(error.error.message, `Unable to update period with ID "${period.id}"`));
                }
            );
        }
    }

    public createNew(loading: EventEmitter<boolean>, value: string) {
        loading.emit(true);
        this.periodService.create(value).subscribe(
            period => {
                this._updatePeriodFormMap.set(period.id, this.createUpdatePeriodForm(period));
                this.periods.push(period);
                this.createModalRef.close();
                loading.emit(false);
                NotificationEmitter.emit(Notification.success('Create successfully'));
            },
            error => {
                loading.emit(false);
                NotificationEmitter.emit(Notification.error(error.error.message, `Unable to create period "${value}"`));
            }
        );
    }

    ngOnInit() {
        this.periodService.get().subscribe(
            periods => {
                for (const period of periods) {
                    this._updatePeriodFormMap.set(period.id, this.createUpdatePeriodForm(period));
                }
                this.periods = periods;
            },
            error => {
                NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve periods'));
            }
        );
    }

    private createUpdatePeriodForm(period: Period): FormGroup {
        return this.fb.group({
            value: [period.value, [Validators.required]],
            startDate: [period.startDate ? moment(period.startDate).format('YYYY-MM-DD') : null, [Validators.pattern(/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])$/)]]
        });
    }

    get updatePeriodFormMap(): Map<number, FormGroup> {
        return this._updatePeriodFormMap;
    }
}
