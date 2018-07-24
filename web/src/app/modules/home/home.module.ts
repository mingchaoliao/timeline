import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {CoreModule} from '../core/core.module';
import {HomeComponent} from './home.component';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {FormsModule} from '@angular/forms';

@NgModule({
  imports: [
    CommonModule,
    CoreModule,
    NgbModule,
    FormsModule
  ],
  declarations: [HomeComponent]
})
export class HomeModule {
}
