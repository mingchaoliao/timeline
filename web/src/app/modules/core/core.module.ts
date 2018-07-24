import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {EventCardComponent} from './components/event-card/event-card.component';
import {ErrorComponent} from './pages/error/error.component';
import {RouterModule} from '@angular/router';
import {HttpService} from './shared/services/http.service';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {CommonService} from './shared/services/common.service';
import {UserService} from './shared/services/user.service';
import {EventService} from './shared/services/event.service';
import {PeriodService} from './shared/services/period.service';
import {CatalogService} from './shared/services/catalog.service';
import {DateAttributeService} from './shared/services/dateAttribute.service';
import {ImageService} from './shared/services/image.service';
import {DateFormatService} from './shared/services/dateFormat.service';
import {TimelineService} from './shared/services/timeline.service';

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    NgbModule
  ],
  declarations: [
    EventCardComponent,
    ErrorComponent
  ],
  exports: [
    EventCardComponent,
    ErrorComponent
  ],
  providers: [
    HttpService,
    CommonService,
    UserService,
    EventService,
    PeriodService,
    CatalogService,
    DateAttributeService,
    ImageService,
    DateFormatService,
    TimelineService
  ]
})
export class CoreModule {
}
