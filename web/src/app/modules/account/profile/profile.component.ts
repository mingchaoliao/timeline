import {Component, OnDestroy, OnInit} from '@angular/core';
import {UserService} from '../../core/shared/services/user.service';
import {User} from '../../core/shared/models/user';
import {Subscription} from 'rxjs';
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {NotificationEmitter} from "../../core/shared/events/notificationEmitter";
import {Notification} from "../../core/shared/models/notification";

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit, OnDestroy {
  private _user: User;
  private _userSubscription: Subscription;
  private _updateProfileSubscription: Subscription;
  private _updatePasswordSubscription: Subscription;
  private readonly _updateProfileForm: FormGroup;
  private readonly _changePasswordForm: FormGroup;

  constructor(private userService: UserService, private fb: FormBuilder) {
    this._updateProfileForm = fb.group({
      'name': [null, [Validators.required]],
    });

    this._changePasswordForm = fb.group({
      'oldPassword': [null, [Validators.required]],
      'newPassword': [null, [Validators.required]],
    });
  }

  ngOnInit() {
    this.userService.getCurrentUser(true).subscribe(
        user => this.user = user
    );
  }

  get user(): User {
    return this._user;
  }

  get updateProfileForm(): FormGroup {
    return this._updateProfileForm;
  }

  get changePasswordForm(): FormGroup {
    return this._changePasswordForm;
  }

  set user(value: User) {
    this._user = value;
  }

  updateProfile() {
    if (this.user && this.updateProfileForm.valid && this.updateProfileForm.dirty) {
      this._updateProfileSubscription = this.userService.updateProfile(this.user.id, this.updateProfileForm.value.name).subscribe(
          user => {
            this.userService.setCurrentUser(user);
            this.updateProfileForm.reset({
              name: user.name
            });
            NotificationEmitter.emit(Notification.success('Update profile successfully'));
          },
          error => {
            NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to update profile'));
          }
      );
    }
  }

  updatePassword() {
    if (this.user && this.changePasswordForm.valid && this.changePasswordForm.dirty) {
      this._updatePasswordSubscription = this.userService.updatePassword(this.user.id, this.changePasswordForm.value.oldPassword, this.changePasswordForm.value.newPassword).subscribe(
          success => {
            this.changePasswordForm.reset();
            NotificationEmitter.emit(Notification.success('Update password successfully'));
          },
          error => {
            NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to update password'));
          }
      );
    }
  }

  ngOnDestroy(): void {
    if (this._userSubscription) {
      this._userSubscription.unsubscribe();
    }

    if (this._updatePasswordSubscription) {
      this._updatePasswordSubscription.unsubscribe();
    }

    if (this._updateProfileSubscription) {
      this._updateProfileSubscription.unsubscribe();
    }
  }
}
