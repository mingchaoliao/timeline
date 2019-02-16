import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {SearchEventFormComponent} from './components/search-event-form/search-event-form.component';
import {SearchEventComponent} from './pages/search-event/search-event.component';
import {GUEST_ROUTING} from './guest.route';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {CoreModule} from '../core/core.module';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {NgSelectModule} from '@ng-select/ng-select';
import {FacetedSearchBarComponent} from './components/faceted-search-bar/faceted-search-bar.component';
import {EventDatepickerComponent} from './components/event-datepicker/event-datepicker.component';
import {TranslateModule} from "@ngx-translate/core";

@NgModule({
  imports: [
    CommonModule,
    GUEST_ROUTING,
    CoreModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    NgSelectModule,
    TranslateModule.forChild()
  ],
  declarations: [
    SearchEventFormComponent,
    SearchEventComponent,
    FacetedSearchBarComponent,
    EventDatepickerComponent
  ]
})
export class GuestModule {
}
