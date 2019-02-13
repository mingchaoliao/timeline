import {Component, EventEmitter, OnDestroy, OnInit, ViewChild} from '@angular/core';
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {UserService} from "../../../core/shared/services/user.service";
import {NotificationEmitter} from "../../../core/shared/events/notificationEmitter";
import {Notification} from "../../../core/shared/models/notification";
import {Subscription} from "rxjs";

@Component({
  selector: 'app-reset-password-modal',
  templateUrl: './reset-password-modal.component.html',
  styleUrls: ['./reset-password-modal.component.css']
})
export class ResetPasswordModalComponent implements OnInit, OnDestroy {
  @ViewChild('resetPasswordModal') private _resetPasswordModal: NgbModalRef;

  private _updatePasswordSubscription: Subscription;
  private _newPassword: string;
  private _uid: number;

  constructor(private modalService: NgbModal, private userService: UserService) {
  }

  ngOnInit() {
  }

  init(uid: number) {
    this._newPassword = null;
    this._uid = uid;
    this.modalService.open(this._resetPasswordModal, {backdrop: 'static'});
  }

  resetPassword(loading: EventEmitter<boolean>) {
    if (this._uid) {
      const newPassword = Math.random().toString(36).slice(-8);
      loading.emit(true);
      this._updatePasswordSubscription = this.userService.updatePassword(this._uid, null, newPassword).subscribe(
          success => {
            this._newPassword = newPassword;
            loading.emit(false);
          },
          error => {
            loading.emit(false);
            NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to reset password'));
          }
      );
    }
  }

  get newPassword(): string {
    return this._newPassword;
  }

  get uid(): number {
    return this._uid;
  }

  ngOnDestroy(): void {
    if (this._updatePasswordSubscription) {
      this._updatePasswordSubscription.unsubscribe();
    }
  }
}
