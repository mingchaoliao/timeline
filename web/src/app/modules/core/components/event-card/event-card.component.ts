import {Component, Input, OnInit} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {CommonService} from '../../shared/services/common.service';
import {UserService} from '../../shared/services/user.service';
import {User} from '../../shared/models/user';
import {Url} from '../../shared/classes/url';
import {EventService} from '../../shared/services/event.service';
import {Notification} from '../../shared/models/notification';
import {NotificationEmitter} from '../../shared/events/notificationEmitter';
import * as moment from 'moment';
import {EventDate} from '../../shared/models/event';
import {Image} from '../../shared/models/image';

@Component({
  selector: 'app-event-card',
  templateUrl: './event-card.component.html',
  styleUrls: ['./event-card.component.css']
})
export class EventCardComponent implements OnInit {

  @Input() data;
  @Input() isPreview = false;
  public isDeleted = false;

  constructor(
    public sanitizer: DomSanitizer,
    public common: CommonService,
    private eventService: EventService) {

  }

  getImageUrl(image: Image): string {
    if (image && image.eventId) {
      return Url.getImage(image.path);
    } else {
      return Url.getTempImage(image.path);
    }
  }

  public getUser(): User {
    return UserService.getCurrentUser();
  }

  ngOnInit() {
  }

  public onDelete(id: number) {
    this.eventService.deleteById(id).subscribe(
      success => {
        this.isDeleted = true;
        NotificationEmitter.emit(Notification.success('Event delete successfully.'));
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message));
      }
    );
  }
}
