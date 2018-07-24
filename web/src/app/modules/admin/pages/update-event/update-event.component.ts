import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {EventService} from '../../../core/shared/services/event.service';

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
            // TODO: handle error
          }
        );
      }
    );
  }

  ngOnInit() {
  }

}
