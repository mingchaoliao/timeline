import { Component, OnInit } from '@angular/core';
import {User} from "../../../core/shared/models/user";
import {UserService} from "../../../core/shared/services/user.service";

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
    if(isChecked !== user.isAdmin) {
      this.userService.grantOrRevokeAdminPrivilege(user.id, isChecked).subscribe(
          s => {
            user.isAdmin = isChecked;
          },
          error => {
            event.srcElement.checked = user.isAdmin;
          }

      );
    }
  }

  ngOnInit() {
  }

}
