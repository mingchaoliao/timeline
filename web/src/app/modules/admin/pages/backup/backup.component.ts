import {Component, EventEmitter, OnDestroy, OnInit} from '@angular/core';
import {BackupService} from '../../services/backup.service';
import {Observable, Subscription} from 'rxjs';
import {Backup} from '../../models/backup';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-backup',
  templateUrl: './backup.component.html',
  styleUrls: ['./backup.component.css']
})
export class BackupComponent implements OnInit, OnDestroy {
  private _backupSubscription: Subscription;
  private _backups: Array<Backup> = [];
  private _backupSummary: Observable<string>;
  private _backupStatus: Observable<string>;
  private _backupCreationLog: string;

  constructor(
      private _backupService: BackupService) {
  }

  ngOnInit() {
    this._backupSubscription = this._backupService.getAll().subscribe(
        backups => this._backups = backups,
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, 'unable to load backups'));
        }
    );

    this._backupSummary = this._backupService.getSummary();
    this._backupStatus = this._backupService.getStatus();
  }

  get backups(): Array<Backup> {
    return this._backups;
  }

  delete(backup: Backup) {
    this._backupService.delete(backup.name).subscribe(
        success => {
          NotificationEmitter.emit(Notification.success('delete backup file successfully'));
          this._backups = this._backups.filter((i: Backup) => backup.name !== i.name);
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, `Unable to delete backup file "${name}"`));
        }
    );
  }

  ngOnDestroy(): void {
    if (this._backupSubscription) {
      this._backupSubscription.unsubscribe();
    }
  }

  get backupSummary(): Observable<string> {
    return this._backupSummary;
  }


  get backupStatus(): Observable<string> {
    return this._backupStatus;
  }

  backupNow(loading: EventEmitter<boolean>) {
    this._backupCreationLog = null;
    loading.emit(true);
    this._backupService.create().subscribe(
        (response: Object) => {
          const backup = Backup.fromJson(response);
          this._backups.push(backup);
          this._backupCreationLog = response['log'];
          loading.emit(false);
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message, `Unable to delete backup file "${name}"`));
          loading.emit(false);
        }
    );
  }

  get backupCreationLog(): string {
    return this._backupCreationLog;
  }
}
