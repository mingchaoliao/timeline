import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {EventService} from '../../../core/shared/services/event.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-update-event',
  templateUrl: './update-event.component.html',
  styleUrls: ['./update-event.component.css']
})
export class UpdateEventComponent implements OnInit {
  public eventData: any = null;

  constructor(
    private eventService: EventService,
    private route: ActivatedRoute
  ) {
    this.route.params.subscribe(
      params => {
        this.eventService.getById(params['id']).subscribe(
          event => this.eventData = event,
          error => {
            NotificationEmitter.emit(Notification.error(error.error.message, `Unable to retrieve event with ID ${params['id']}`));
          }
        );
      }
    );
  }

  ngOnInit() {
  }

}
