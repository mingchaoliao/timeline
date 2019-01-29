import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {SearchEventFormComponent} from './components/search-event-form/search-event-form.component';
import {AboutComponent} from './pages/about/about.component';
import {SearchEventComponent} from './pages/search-event/search-event.component';
import {GUEST_ROUTING} from './guest.route';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {CoreModule} from '../core/core.module';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {NgSelectModule} from '@ng-select/ng-select';
import { FacetedSearchBarComponent } from './components/faceted-search-bar/faceted-search-bar.component';
import { EventsComponent } from './pages/events/events.component';
import { EventHitCardComponent } from './components/event-hit-card/event-hit-card.component';

@NgModule({
  imports: [
    CommonModule,
    GUEST_ROUTING,
    CoreModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    NgSelectModule
  ],
  declarations: [
    SearchEventFormComponent,
    AboutComponent,
    SearchEventComponent,
    FacetedSearchBarComponent,
    EventsComponent,
    EventHitCardComponent
  ]
})
export class GuestModule {
}
