import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {CoreModule} from '../core/core.module';
import {HomeComponent} from './home.component';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {FormsModule} from '@angular/forms';
import {RouterModule} from '@angular/router';
import {TranslateModule} from "@ngx-translate/core";

@NgModule({
  imports: [
    CommonModule,
    CoreModule,
    NgbModule,
    FormsModule,
    RouterModule,
    TranslateModule.forChild()
  ],
  declarations: [HomeComponent]
})
export class HomeModule {
}
