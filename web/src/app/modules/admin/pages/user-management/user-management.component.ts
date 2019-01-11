import { Component, OnInit } from '@angular/core';
import {User} from "../../../core/shared/models/user";
import {UserService} from "../../../core/shared/services/user.service";
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-user-management',
  templateUrl: './user-management.component.html',
  styleUrls: ['./user-management.component.css']
})
export class UserManagementComponent implements OnInit {

  public users: Array<User> = null;

  constructor(public userService: UserService) {
    userService.getAll().subscribe(
        s => {
          this.users = s;
        }
    );
  }

  public grantOrRevokeAdminFor(user: User, event) {
    const isChecked = event.srcElement.checked;
    user['isLoading'] = true;
    if(isChecked !== user.isAdmin) {
      this.userService.grantOrRevokeAdminPrivilege(user.id, isChecked).subscribe(
          s => {
            user.isAdmin = isChecked;
            user['isLoading'] = false;
            NotificationEmitter.emit(Notification.success('Grant/revoke successfully'));
          },
          error => {
            event.srcElement.checked = user.isAdmin;
            user['isLoading'] = false;
            NotificationEmitter.emit(Notification.error(error.error.message, `Unable to grant/revoke admin privilege to "${user.name}"`));
          }

      );
    }
  }

  ngOnInit() {
  }

}
