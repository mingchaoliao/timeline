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
import {NotificationComponent} from './components/notification/notification.component';
import {ButtonComponent} from './components/button/button.component';
import {AutofocusDirective} from './shared/directives/autofocus.directive';
import {TranslateModule} from "@ngx-translate/core";

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    NgbModule,
    TranslateModule.forChild()
  ],
  declarations: [
    EventCardComponent,
    ErrorComponent,
    NotificationComponent,
    ButtonComponent,
    AutofocusDirective
  ],
  exports: [
    EventCardComponent,
    ErrorComponent,
    NotificationComponent,
    ButtonComponent,
    AutofocusDirective
  ],
  providers: [
    HttpService,
    CommonService,
    UserService,
    EventService,
    PeriodService,
    CatalogService,
    DateAttributeService,
    ImageService
  ]
})
export class CoreModule {
}
