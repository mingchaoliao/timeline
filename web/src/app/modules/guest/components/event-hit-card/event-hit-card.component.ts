import {Component, Input, OnInit, ViewEncapsulation} from '@angular/core';
import {EventHit} from '../../../core/shared/models/eventSearchResult';
import {DomSanitizer} from '@angular/platform-browser';

@Component({
  selector: 'app-event-hit-card',
  templateUrl: './event-hit-card.component.html',
  styleUrls: ['./event-hit-card.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class EventHitCardComponent implements OnInit {

  @Input('hit') hit: EventHit;

  constructor(public sanitizer: DomSanitizer) {
  }

  ngOnInit() {
  }

}
