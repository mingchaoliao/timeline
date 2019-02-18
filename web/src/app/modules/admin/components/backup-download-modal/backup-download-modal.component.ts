import {Component, ElementRef, EventEmitter, OnInit, ViewChild} from '@angular/core';
import {NgbModal, NgbModalRef} from '@ng-bootstrap/ng-bootstrap';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {Backup} from '../../models/backup';
import {Notification} from '../../../core/shared/models/notification';
import {BackupService} from '../../services/backup.service';
import {saveAs} from 'file-saver';
import {NotificationEmitter} from "../../../core/shared/events/notificationEmitter";

@Component({
  selector: 'app-backup-download-modal',
  templateUrl: './backup-download-modal.component.html',
  styleUrls: ['./backup-download-modal.component.css']
})
export class BackupDownloadModalComponent implements OnInit {
  @ViewChild('downloadModal') private _downloadModal: ElementRef;
  private _downloadModalRef: NgbModalRef;
  private readonly _accountVerificationForm: FormGroup;
  private _backup: Backup;
  private _errorMessage: string;

  constructor(private _fb: FormBuilder,
              private _modalService: NgbModal,
              private _backupService: BackupService) {
    this._accountVerificationForm = _fb.group({
      password: [null, [Validators.required]]
    });
  }

  ngOnInit() {
  }

  open(backup: Backup) {
    this._backup = backup;
    this._accountVerificationForm.reset();
    this._errorMessage = null;
    this._downloadModalRef = this._modalService.open(this._downloadModal, {
      backdrop: 'static'
    });
  }

  get accountVerificationForm(): FormGroup {
    return this._accountVerificationForm;
  }

  download(loading: EventEmitter<boolean>) {
    loading.emit(true);
    this._backupService.download(this._backup.name, this._accountVerificationForm.value.password).subscribe(
        (data: Response) => {
          const blob = new Blob([data], {type: 'application/zip'});
          saveAs(blob, this._backup.name);
          loading.emit(false);
          this._downloadModalRef.close();
        },
        error => {
          loading.emit(false);
          if (error.status === 403) {
            this._errorMessage = 'credential does not match';
          } else {
            NotificationEmitter.emit(Notification.error('Unable to download backup file'));
            this._downloadModalRef.close();
          }
        }
    );
  }

  get errorMessage(): string {
    return this._errorMessage;
  }
}
