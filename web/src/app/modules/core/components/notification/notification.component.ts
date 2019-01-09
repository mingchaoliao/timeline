import {Component, OnInit} from '@angular/core';
import {Notification} from '../../shared/models/notification';
import {NotificationEmitter} from '../../shared/events/notificationEmitter';

@Component({
  selector: 'app-notification',
  templateUrl: './notification.component.html',
  styleUrls: ['./notification.component.css']
})
export class NotificationComponent implements OnInit {

  public notifications: Array<Notification> = [];

  constructor() {
  }

  ngOnInit() {
    NotificationEmitter.emitter.subscribe(notification => {
      setTimeout(() => notification.isTimeout = true, notification.timeout * 1000);
      this.notifications.push(notification);
    });
  }

  close(notification: Notification) {
    notification.isTimeout = true;
  }
}
