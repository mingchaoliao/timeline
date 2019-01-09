///<reference path="../../../../node_modules/@angular/core/src/metadata/lifecycle_hooks.d.ts"/>
import {AfterViewInit, Component, OnInit} from '@angular/core';
import {CommonService} from '../core/shared/services/common.service';
import {EventService} from '../core/shared/services/event.service';
import {Event} from '../core/shared/models/event';
import {TimelineService} from '../core/shared/services/timeline.service';
import {Notification} from '../core/shared/models/notification';
import {NotificationEmitter} from '../core/shared/events/notificationEmitter';

declare var TL: any;

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit, AfterViewInit {
  public events: Array<Event> = [];
  public displayMethod = 'timeline';
  public total = 1;
  public page = 1;
  public pageSize = 10;

  constructor(
    private common: CommonService,
    private eventService: EventService,
    private timelineService: TimelineService
  ) {

  }

  ngOnInit() {
  }

  ngAfterViewInit(): void {
    this.getTimeline();
  }

  getTimeline() {
    this.timelineService.get().subscribe(
      timeline => {
        new TL.Timeline('timeline', timeline);
      },
      error => {
        console.log(error);
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve events'));
      }
    );
  }

  public onDisplayMethodChange() {
    if (this.displayMethod === 'list') {
      this.page = 1;
      this.eventService.get().subscribe(
        events => {
          this.total = events['total'];
          this.events = events;
        },
        error => {
          console.log(error.error.message);
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve events'));
        }
      );
    } else if (this.displayMethod === 'timeline') {
      this.getTimeline();
    }
  }

  public onPageChange(page) {
    this.events = [];
    this.eventService.get(page).subscribe(
      events => {
        this.total = events['total'];
        this.events = events;
      },
      error => {
        console.log(error.error.message);
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve events'));
      }
    );
  }
}
