import {Component, EventEmitter, OnInit} from '@angular/core';
import {PeriodService} from '../../../core/shared/services/period.service';
import {Period} from '../../../core/shared/models/period';
import {NgbModal, NgbModalRef} from '@ng-bootstrap/ng-bootstrap';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';
import {FormBuilder, FormGroup, Validators} from "@angular/forms";

@Component({
    selector: 'app-configure-periods',
    templateUrl: './configure-periods.component.html',
    styleUrls: ['./configure-periods.component.css']
})
export class ConfigurePeriodsComponent implements OnInit {

    public periods: Array<Period> = null;
    public createModalRef: NgbModalRef = null;
    private readonly _createPeriodForm: FormGroup;

    constructor(
        public periodService: PeriodService,
        public modalService: NgbModal,
        private fb: FormBuilder
    ) {
        this._createPeriodForm = fb.group({
            value: [null, [Validators.required]]
        });
        periodService.get().subscribe(
            s => {
                this.periods = s;
            },
            error => {
                NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve periods'));
            }
        );
    }

    get createPeriodForm(): FormGroup {
        return this._createPeriodForm;
    }

    public delete(loading: EventEmitter<boolean>, index: number) {
        loading.emit(true);
        const period = this.periods[index];
        this.periodService.delete(period.id).subscribe(
            s => {
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

    public update(loading: EventEmitter<boolean>, index: number, value: string) {
        loading.emit(true);
        const period = this.periods[index];
        this.periodService.update(period.id, value).subscribe(
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

    public createNew(loading: EventEmitter<boolean>, value: string) {
        loading.emit(true);
        this.periodService.create(value).subscribe(
            s => {
                this.periods.push(s);
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
    }

}
