import {Component} from '@angular/core';
import {UserService} from './modules/core/shared/services/user.service';
import * as moment from 'moment';
import {environment} from '../environments/environment';
import {NotificationEmitter} from './modules/core/shared/events/notificationEmitter';
import {Notification} from './modules/core/shared/models/notification';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  public title = environment.title;
  public currentYear: string;
  public company: any;

  constructor(private userService: UserService) {
    this.userService.getCurrentUser().subscribe(
      user => {
      },
      error => {
      }
    );

    this.currentYear = moment().format('YYYY');
    this.company = environment.company;
  }

  getUser() {
    return UserService.getCurrentUser();
  }

  public logout() {
    UserService.logout();
  }
}
