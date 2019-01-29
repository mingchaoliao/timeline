///<reference path="../../../../node_modules/@angular/core/src/metadata/lifecycle_hooks.d.ts"/>
import {AfterViewInit, Component, OnInit} from '@angular/core';
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

  constructor(
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
}
